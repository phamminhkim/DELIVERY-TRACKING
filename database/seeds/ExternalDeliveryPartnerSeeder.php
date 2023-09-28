<?php

use App\Models\Master\ApiDeliveryPartnerMapping;
use App\Models\Master\DeliveryPartner;
use Illuminate\Database\Seeder;

class ExternalDeliveryPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $delivery_partners = array(
            array('code' => 'VIN', 'name' => 'VINSTRAN', 'api_url' => 'http://api.vintrans.com.vn:8989/api/customer/trackinglading', 'api_method' => 'GET', 'api_key' => '', 'api_secret' => '', 'api_body_datas' => array(
                "Username" => "SGN0450BTN",
                "Password" => "vts@2022"
            ), 'api_delivery_code_field' => 'Code', 'is_external' => '1', 'is_active' => '1', 'created_at' => '2023-09-26 12:45:26', 'updated_at' => '2023-09-26 12:45:26', 'deleted_at' => NULL),
            array('code' => 'NET', 'name' => 'NETCO', 'api_url' => 'https://api-v4-erp.vps.vn/api/V1Customer/APIC_spLadingGet', 'api_method' => 'GET', 'api_key' => '', 'api_secret' => '', 'api_body_datas' => array(
                "CustomerCode" => "SGN14174SGN",
                "AppAPIKey" => "0UtiJ3ZNav5HPf2jzdby4A=="
            ), 'api_delivery_code_field' => 'LadingCode', 'is_external' => '1', 'is_active' => '1', 'created_at' => '2023-09-26 15:55:26', 'updated_at' => '2023-09-26 16:17:51', 'deleted_at' => NULL)
        );
        $api_delivery_partner_mappings = array(
            array('delivery_partner_id' => '3', 'root_data_field' => '', 'is_root_string' => false, 'start_delivery_date' => 'MiningDay', 'complete_delivery_date' => 'FinishDate'),
            array('delivery_partner_id' => '4', 'root_data_field' => 'data', 'is_root_string' => true, 'start_delivery_date' => 'CreateDate', 'complete_delivery_date' => 'FinishDate')
        );
        foreach ($delivery_partners as $index => $delivery_partner) {
            if (!DeliveryPartner::where('code', $delivery_partner['code'])->exists()) {
                $delivery_partner = DeliveryPartner::create($delivery_partner);
                $api_delivery_partner_mappings[$index]['delivery_partner_id'] = $delivery_partner->id;
                ApiDeliveryPartnerMapping::create($api_delivery_partner_mappings[$index]);
            }
        }
        $this->command->info('ExternalDeliveryPartnerSeeder has been completed!');
    }
}
