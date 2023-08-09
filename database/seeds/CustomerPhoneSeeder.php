<?php

use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use Illuminate\Database\Seeder;

class CustomerPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customerPhones = [
            [
                'code' => '600128',
                'name' => 'NGUYỄN THỊ ĐỊNH',
                'phone_number' => "0343084437",
                'is_active' => true,
            ],
           
        ];

        foreach ($customerPhones as $customerPhone) {
            $customer = Customer::where('code', $customerPhone['code'])->first();
            if ($customer){
                $customerPhone['customer_id'] = $customer->id;

                CustomerPhone::create([

                    'customer_id' => $customer->id,
                    'name' => $customerPhone['name'] ,
                    'description' => $customerPhone['name'] ,
                    'phone_number' => $customerPhone['phone_number'] ,
                    'is_active' => true,
                ]);
            }
           
        }

        $this->command->info('CompanySeeder has been completed!');
    }
}
