<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserBank;
use App\Models\Bank;
use App\Models\User;

class UserBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserBank::create([
            'bank_id' => Bank::where('name', 'Mandiri')->first()->id,
            'user_id' => User::where('username', 'indraaa')->first()->id,
            'account_name' => 'Indra Wahyu',
            'va_number' => '9678 0 5673 4512 3349',
            'rekening_number' => '1310020259539',
        ]);
    }
}
