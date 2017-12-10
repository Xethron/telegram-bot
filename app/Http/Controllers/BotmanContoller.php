<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Http\Request;

class BotmanContoller extends Controller
{
    public function handle()
    {
        DriverManager::loadDriver(TelegramDriver::class);

        /** @var BotMan $botman */
        $botman = app('botman');

        $botman->hears('/getBTCEquivalent {value} {currency}', function (BotMan $bot, $value, $currency) {
            $json = json_decode(file_get_contents('https://api.coindesk.com/v1/bpi/currentprice/'.$currency.'.json'));

            $exchangeRate = $json->bpi->{$currency}->rate_float;

            $valueInBtc = $value/$exchangeRate;

            $bot->reply("$value $currency is ".number_format($valueInBtc, 6)." BTC (".number_format($exchangeRate, 4)." $currency - 1 BTC)");
        });

        $botman->hears('/getUserID', function (BotMan $bot) {
            $bot->reply(2);
        });

        $botman->listen();
    }
}
