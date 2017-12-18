<?php

namespace App\Coinbase;

class CurrentPrice
{
    /**
     * @var string Currency
     */
    private $currency;

    /**
     * @var \Object Data
     */
    private $data;

    public function __construct($currency, $data)
    {
        $this->currency = $currency;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getExchangeRate(): float
    {
        return $this->data->bpi->{$this->currency}->rate_float;
    }

    public function getValueInBitcoin(float $value = 1): float
    {
        return $value / $this->getExchangeRate();
    }

    public function __get($name)
    {
        $getterName = 'get'.studly_case($name);
        if (method_exists($this, $getterName)) {
            return $this->{$getterName}();
        }
    }
}
