<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Pail\ValueObjects\Origin\Console;

class DataTrasferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $client = base_path('storage/app/private/customers.json');
            $data = json_decode(file_get_contents($client), true);
            $customers = collect($data[2]['data']);

            foreach ($customers as $item) {
                $client = Client::updateOrCreate(
                    ['email' => $item['email']],
                    [
                        'name' => $item['name'],
                        'phone' => $item['contact_no'] ?? null,
                        'password' => Hash::make(str_replace(' ', '_', $item['name']) . '123'),
                        'created_at' => $item['created_at'] ?? null,
                        'updated_at' => $item['updated_at'] ?? null,
                    ]
                );
                $client->assignRole('client');
                Log::info($item['name']);
            }
            $invoices = base_path('storage/app/private/links.json');
            $data = json_decode(file_get_contents($invoices), true);
            $koderspedia_brand_id = Brand::where('name', 'Koderspedia')->firstOrFail()->id;
            foreach ($data[2]['data'] as $item) {
                
                $customer_email = $customers->firstWhere('id', $item['customer_id'])['email'];
                $category = Category::where(function ($query) use ($item) {
                    $name = strtolower($item['item_category']);
                    $query->whereRaw('LOWER(name) = ?', [$name])
                        ->orWhereRaw('LOWER(name) LIKE ?', ["%{$name}%"]);
                })->first();
                if (!$category) {
                    $category = Category::create(['name' => $item['item_category']]);
                }
                $brand_name = $item['client_of'];
                $brand = Brand::where(function ($query) use ($brand_name) {
                    $query->orWhere('name', 'LIKE', "%{$brand_name}%");
                })->first();
                $payment_type_id = PaymentType::where('name', $item['payment_type'])->firstOrFail()->id;

                $invoicesIndb = Invoice::create(
                    [
                        'user_id' => 1,
                        'client_id' => Client::where('email', $customer_email)->firstOrFail()->id,
                        'title' => $item['item_name'],
                        'price' => $item['item_price'],
                        'remaining_price' => $item['remaining_price'],
                        'status' => $item['is_paid'],
                        'description' => $item['item_description'],
                        'category_id' => $category->id,
                        'payment_type_id' => $payment_type_id,
                        'brand_id' => $brand->id ?? $koderspedia_brand_id,
                        'sale_type' => $item['sale_type'],
                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],
                    ]
                );
                Log::info(json_encode([
                    'invoice' => $invoicesIndb->only(['id', 'user_id', 'client_id', 'category_id', 'payment_type_id', 'brand_id']),
                    'brand' => $brand->id ?? $brand_name
                ]));
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Data transfer failed: ' . $th->getMessage());
        }
    }
}
