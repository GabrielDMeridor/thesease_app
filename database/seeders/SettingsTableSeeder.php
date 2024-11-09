<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        Setting::updateOrCreate(
            ['key' => 'submission_files_link'],
            ['value' => 'https://default-global-link.com']
        );
    }
}

