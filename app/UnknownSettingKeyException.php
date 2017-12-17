<?php

namespace App;

/**
 * UnknownSettingKeyException
 */
class UnknownSettingKeyException extends \InvalidArgumentException
{
    /**
     * UnknownSettingKeyException constructor.
     *
     * @param $key
     */
    public function __construct($key)
    {
        parent::__construct('Unknown Setting Key: "'.$key.'".');
    }
}
