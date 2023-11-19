<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = ['Mandiri', 'BCA', 'BNI', 'BRI', 'BSI'];

        foreach ($banks as $bank) {
            Bank::create([
                'name' => $bank,
            ]);
        }
    }
}
