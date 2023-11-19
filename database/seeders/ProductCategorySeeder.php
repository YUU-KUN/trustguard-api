<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product_categories = [
            'Barang Elektronik',
            'Kendaraan',
            'Makanan atau Minuman',
            'Investasi',
            'Produk Digital',
            'Pulsa atau Paket Internet',
            'Fashion dan Kecantikan',
            'Tiket Event atau Wisata',
            'Penipuan Berhadiah',
            'Perlengkapan Rumah Tangga',
            'Obat-obatan',
            'Jam dan Arloji',
            'Perlengkapan Bayi dan Anak',
            'Olahraga dan Outdoor',
            'Mainan dan Hobi',
        ];

        foreach ($product_categories as $category) {
            ProductCategory::create([
                'name' => $category
            ]);
        }
    }
}
