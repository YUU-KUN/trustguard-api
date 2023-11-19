<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'firstname' => 'Indra',
            'lastname' => 'Wahyu',
            'dob_place' => 'Pekanbaru',
            'dob' => '2002-10-12',
            'gender' => 'male',
            'nik' => '1471061210020021',
            'phone' => '081358282255',
            'balance' => 1000000,

            'username' => 'indraaa',
            'password' => bcrypt('password'),
        ]);

        
    }
}
