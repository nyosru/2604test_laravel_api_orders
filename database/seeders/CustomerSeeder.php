<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Ivan Petrov', 'email' => 'ivan.petrov@example.com', 'phone' => '+79001000001'],
            ['name' => 'Anna Sidorova', 'email' => 'anna.sidorova@example.com', 'phone' => '+79001000002'],
            ['name' => 'Pavel Smirnov', 'email' => 'pavel.smirnov@example.com', 'phone' => '+79001000003'],
            ['name' => 'Elena Volkova', 'email' => 'elena.volkova@example.com', 'phone' => '+79001000004'],
            ['name' => 'Dmitry Orlov', 'email' => 'dmitry.orlov@example.com', 'phone' => '+79001000005'],
        ];

        foreach ($customers as $customer) {
            Customer::query()->updateOrCreate(
                ['email' => $customer['email']],
                $customer,
            );
        }

        Customer::factory(10)->create();
    }
}
