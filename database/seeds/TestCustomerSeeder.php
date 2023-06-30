<?php

use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Models\Master\Employee;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total_customer = $this->command->ask(
            'How many customers you want to create?',
            10
        );
        $total_phone_number = $this->command->ask(
            'How many phone numbers of customer you want to create?',
            10
        );

        for ($i = 1; $i <= $total_customer; $i++) {
            $this->command->info('Creating customer ' . $i . ' of ' . $total_customer);

            $customer_code = 'C' . sprintf('%02d', $i);
            $customer_name = 'customer' . sprintf('%02d', $i);

            if (Customer::where('code', $customer_code)->exists()) {
                $this->command->warn('Customer \'' . $customer_code . '\' already exists');
                continue;
            }

            $customer = Customer::create([
                'code' => $customer_code,
                'name' => $customer_name,
                'email' => $customer_code . '@customer.local',
                'phone_number' => '170000' . sprintf('%02d', $i),
                'address' => 'Address of ' . $customer_name,
            ]);

            $this->command->info('Customer \'' . $customer_name . '\' created');

            for ($j = 1; $j <= $total_phone_number; $j++) {
                $customer->phones()->create([
                    'phone_number' => '1800' .  sprintf('%02d', $i) . sprintf('%02d', $j),
                    'name' => $customer_code . ' phone' . sprintf('%02d', $j),
                    'description' => 'Description of ' . $customer_code . ' phone' . sprintf('%02d', $j),
                    'is_active' => true,
                ]);
            }

            $this->command->info('Customer \'' . $customer_name . '\' phones created');
        }
    }
}
