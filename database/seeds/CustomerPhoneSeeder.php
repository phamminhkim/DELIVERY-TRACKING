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
            array("code"=>"102056","name"=>"NGUYỄN THỊ ĐỊNH","phone_number"=>"0343084437"),
        ];

        foreach ($customerPhones as $customerPhone) {
            $customer = Customer::where('code', $customerPhone['code'])->first();
            if ($customer){
                $customerPhone['customer_id'] = $customer->id;
                $phone_number = "84" . substr($customerPhone['phone_number'],1, strlen($customerPhone['phone_number'])-1);
                if(!CustomerPhone::where('customer_id',$customer->id)->where('phone_number', $phone_number)->exists()){
                    CustomerPhone::create([

                        'customer_id' => $customer->id,
                        'name' => $customerPhone['name'] ,
                        'description' => $customerPhone['name'] ,
                        'phone_number' => $phone_number ,
                        'is_active' => true,
                    ]);
                }

            }
           
        }

        $this->command->info('CompanySeeder has been completed!');
    }
}
