<?php

namespace Database\Seeders;

use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker       = Faker::create('en_US');
        $phoneSuffixes = ['13', '14', '15', '16', '17', '18', '19'];
        $usedPhones  = [];
        $count       = 0;
        $attempts    = 0;

        while ($count < 50 && $attempts < 200) {
            $attempts++;

            $phone = '0' . $faker->randomElement($phoneSuffixes) . $faker->numerify('########');

            if (in_array($phone, $usedPhones, true)) {
                continue;
            }

            if (Customer::where('phone', $phone)->exists()) {
                continue;
            }

            $usedPhones[] = $phone;

            Customer::create([
                'name'           => $faker->name(),
                'phone'          => $phone,
                'email'          => $faker->boolean(50) ? $faker->safeEmail() : null,
                'nid'            => $faker->boolean(30) ? $faker->numerify('##############') : null,
                'organization'   => $faker->boolean(40) ? $faker->company() : null,
                'address'        => $faker->boolean(60) ? $faker->address() : null,
                'credit_balance' => $faker->randomFloat(2, 0, 5000),
                'created_by'     => 1,
            ]);

            $count++;
        }

        $this->command->info("✓ {$count} customers created.");
    }
}
