<?php

namespace App\Http\Controllers;

use App\Account;
use App\BotConfig;
use App\Coinbase\Coinbase;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use BotMan\BotMan\Users\User as BotManUser;
use BotMan\Drivers\Telegram\TelegramDriver;

class BotmanContoller extends Controller
{
    /**
     * @var Coinbase
     */
    private $coinbase;

    public function __construct(Coinbase $coinbase)
    {
        $this->coinbase = $coinbase;
    }

    public function handle()
    {
        DriverManager::loadDriver(TelegramDriver::class);

        $config = [
            'telegram' => [
                'token' => BotConfig::get('token'),
            ]
        ];

        /** @var BotMan $botman */
        $botman =  BotManFactory::create($config, new LaravelCache(), app('request'),
            new FileStorage(storage_path('botman')));

        $botman->hears('/start', function (BotMan $bot) {
            $bot->reply($this->getAvailableCommands());
        });

        $botman->hears('/help', function (BotMan $bot) {
            $bot->reply($this->getAvailableCommands());
        });

        $botman->fallback(function($bot) {
            $bot->reply("Sorry, I did not understand these commands. Here is a list of commands I understand:\n\n".$this->getAvailableCommands());
        });

        $botman->hears('/getBTCEquivalent {message}', function (BotMan $bot, $message) {
            $messageParts = explode(' ', $message);
            if (count($messageParts) === 1) {
                $messageParts[]  = BotConfig::get('currency');
            }
            $bot->reply($this->getBitcoinEquivalent($messageParts[0], $messageParts[1]));
        });

        $botman->hears('/getBTCEquivalent', function (BotMan $bot) {
            $bot->reply('Please specify the value as well as the currency, eg: /getBTCEquivalent 100 '.BotConfig::get('currency'));
        });

        $botman->hears('/getUserID', function (BotMan $bot) {
            $bot->reply($this->getAccount($bot->getUser())->id);
        });

        $botman->listen();
    }

    /**
     * @param BotManUser $user
     *
     * @return Account
     */
    public function getAccount(BotManUser $user)
    {
        $account = Account::where('account_id', $user->getId())->first();

        if (!$account) {
            $account = new Account();
            $account->account_id = $user->getId();
            $account->first_name = $user->getFirstName();
            $account->last_name = $user->getFirstName();
            $account->save();
        }

        return $account;
    }

    /**
     * @param $value
     * @param $currency
     *
     * @return string
     */
    public function getBitcoinEquivalent($value, $currency)
    {
        $currentPrice = $this->coinbase->getCurrentPrice($currency);
        $exchangeRate = $currentPrice->getExchangeRate();
        $valueInBtc = $currentPrice->getValueInBitcoin($value);

        $message = "$value {$currentPrice->currency} is ".number_format($valueInBtc, 6)." BTC (".number_format($exchangeRate, 4)." {$currentPrice->currency} - 1 BTC)";

        return $message;
    }

    private function getAvailableCommands()
    {
        return "Available commands are:\n\n".
        '/getBTCEquivalent {value} {currency} # Returns the Bitcoin equivalent for a specific currency.
         eg: /getBTCEquivalent 100 '.BotConfig::get('currency')."\n".
        "/getUserID # Returns your registered user ID for the system\n".
        '/help # Returns this list of possible commands';
    }
}
