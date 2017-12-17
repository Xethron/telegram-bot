<?php

namespace Tests\Unit;

use App\Setting;
use App\BotConfig;
use App\UnknownSettingKeyException;
use Cache;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BotConfigTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function canGetValuesFromDatabase()
    {
        Setting::create([
            'key' => 'currency',
            'value' => 'USD',
        ]);

        $this->assertEquals('USD', BotConfig::get('currency'));
    }

    /** @test */
    public function storesValuesInCache()
    {
        Setting::create([
            'key' => 'currency',
            'value' => 'USD',
        ]);

        BotConfig::get('currency');

        $this->assertEquals('USD', Cache::get(BotConfig::CACHE_PREFIX.'currency'));
    }

    /** @test */
    public function preferCachedValues()
    {
        Setting::create([
            'key' => 'currency',
            'value' => 'USD',
        ]);

        Cache::put(BotConfig::CACHE_PREFIX.'currency', 'ZAR', 1);

        $this->assertEquals('ZAR', BotConfig::get('currency'));
    }

    /** @test */
    public function settingValueUpdatesDatabaseAndCache()
    {
        BotConfig::set('currency', 'ABC');

        $this->assertEquals('ABC', Setting::find('currency')->value);
        $this->assertEquals('ABC', Cache::get(BotConfig::CACHE_PREFIX.'currency'));
    }

    /** @test */
    public function unknownKeysShouldThrowAnException()
    {
        try {
            BotConfig::get('unknown');
        } catch (UnknownSettingKeyException $e) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->fail('Expected exception to be thrown.');
    }
}
