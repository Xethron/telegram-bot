<?php

namespace App\Coinbase;

class Coinbase
{
    public function getCurrentPrice($currency): CurrentPrice
    {
        $currency = strtoupper($currency);
        $json = json_decode(file_get_contents('https://api.coindesk.com/v1/bpi/currentprice/'.$currency.'.json'));

        return new CurrentPrice($currency, $json);
    }
}
