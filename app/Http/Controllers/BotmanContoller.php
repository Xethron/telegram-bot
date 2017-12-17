<?php

namespace App\Http\Controllers;

use App\Account;
use App\BotConfig;
use App\Setting;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use BotMan\BotMan\Users\User as BotManUser;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Http\Request;

class BotmanContoller extends Controller
{
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

        $botman->hears('/getBTCEquivalent {message}', function (BotMan $bot, $message) {
            $messageParts = explode(' ', $message);
            if (count($messageParts) === 1) {
                $messageParts[]  = BotConfig::get('currency');
            }
            $bot->reply($this->getBitcoinEquivalent($messageParts[0], $messageParts[1]));
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
        $json = json_decode(file_get_contents('https://api.coindesk.com/v1/bpi/currentprice/'.$currency.'.json'));

        $exchangeRate = $json->bpi->{$currency}->rate_float;

        $valueInBtc = $value / $exchangeRate;

        $message = "$value $currency is ".number_format($valueInBtc, 6)." BTC (".number_format($exchangeRate, 4)." $currency - 1 BTC)";

        return $message;
    }
}
