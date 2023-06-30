<?php

use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Models\Master\DeliveryPartner;
use App\Models\Master\Employee;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total_partner = $this->command->ask(
            'How many delivery partners you want to create?',
            10
        );

        for ($i = 1; $i <= $total_partner; $i++) {
            $this->command->info('Creating partner ' . $i . ' of ' . $total_partner);

            $partner_code = 'P' . sprintf('%02d', $i);
            $partner_name = 'partner' . sprintf('%02d', $i);

            if (DeliveryPartner::where('code', $partner_code)->exists()) {
                $this->command->warn('Partner \'' . $partner_code . '\' already exists');
                continue;
            }

            DeliveryPartner::create([
                'code' => $partner_code,
                'name' => $partner_name,
                'api_url' => 'http://delivery.thienlong.com:8000',
                'api_key' => 'apikey_' . $partner_code,
                'api_secret' => 'apisecret_' . $partner_code,
                'is_external' => true,
                'is_active' => true,
            ]);

            $this->command->info('Partner \'' . $partner_name . '\' created');
        }
    }
}
