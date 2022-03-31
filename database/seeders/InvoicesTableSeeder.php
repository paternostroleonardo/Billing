<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Faker\Factory;
use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Invoice::truncate();
        InvoiceItem::truncate();

        foreach (range(1, 25) as $i) {
            $invoice = Invoice::create([
                'number' => 'INV-2000' . $i,
                'issuer_name' => $faker->firstName,
                'issuer_nit' => $faker->phoneNumber,
                'receiver_name' => $faker->firstName,
                'receiver_nit' => $faker->phoneNumber,
                'date' => '2022-04-' . $i,
                'due_date' => '2023-01-' . $i,
                'discount' => mt_rand(0, 100),
                'sub_total' => mt_rand(1000, 2000)
            ]);

            foreach (range(1, mt_rand(2, 4)) as $j) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => mt_rand(1, 40),
                    'unit_price' => mt_rand(100, 500),
                    'qty' => mt_rand(1, 6)
                ]);
            }
        }
    }
}
