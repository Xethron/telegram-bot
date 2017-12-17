<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Setting::create([
            'key' => 'url',
            'value' => 'https://example.com',
        ]);

        \App\Setting::create([
            'key' => 'token',
            'value' => '',
        ]);

        \App\Setting::create([
            'key' => 'currency',
            'value' => 'ZAR',
        ]);
    }
}
