<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Platform;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $platforms = ['Instagram', 'LINE', 'Facebook', 'Twitter', 'WhatsApp', 'Situs Web', 'Aplikasi Mobile', 'Lainnya'];
        foreach ($platforms as $platform) {
            Platform::create([
                'name' => $platform
            ]);
        }
    }
}
