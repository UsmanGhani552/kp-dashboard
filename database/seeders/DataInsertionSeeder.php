<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PackageDeliverables;
use App\Models\PaymentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataInsertionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Website'],
            ['name' => 'Mobile App'],
            ['name' => 'Social Media'],
            ['name' => 'Seo'],
        ];
        foreach ($categories as $category) {
            Category::updateOrInsert(['name' => $category['name']], $category);
        }

        $payment_types = [
            'stripe',
            'paypal',
            'square',
        ];
        foreach ($payment_types as $type) {
            PaymentType::updateOrCreate(['name' => $type]);
        }
    }
}
