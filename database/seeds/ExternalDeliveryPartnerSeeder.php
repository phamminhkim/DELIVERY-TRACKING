<?php

use App\Models\Business\ApiDeliveryPartnerConfig;
use App\Models\Business\ApiDeliveryPartnerRequest;
use App\Models\Master\ApiDeliveryPartnerMapping;
use App\Models\Master\DeliveryPartner;
use App\Models\Master\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            array('code' => 'VIN', 'name' => 'VINSTRAN', 'api_url' => 'http://api.vintrans.com.vn:8989/api/customer/trackinglading', 'api_method' => 'GET', 'api_key' => '', 'api_secret' => '', 'api_body_datas' => json_encode(array(
                "Username" => "SGN0450BTN",
                "Password" => "vts@2022"
            )), 'api_delivery_code_field' => 'Code', 'is_external' => '1', 'is_active' => '1', 'created_at' => '2023-09-26 12:45:26', 'updated_at' => '2023-09-26 12:45:26', 'deleted_at' => NULL),
            array('code' => 'NET', 'name' => 'NETCO', 'api_url' => 'https://api-v4-erp.vps.vn/api/V1Customer/APIC_spLadingGet', 'api_method' => 'GET', 'api_key' => '', 'api_secret' => '', 'api_body_datas' => json_encode(array(
                "CustomerCode" => "SGN14174SGN",
                "AppAPIKey" => "0UtiJ3ZNav5HPf2jzdby4A=="
            )), 'api_delivery_code_field' => 'LadingCode', 'is_external' => '1', 'is_active' => '1', 'created_at' => '2023-09-26 15:55:26', 'updated_at' => '2023-09-26 16:17:51', 'deleted_at' => NULL)
        );

        $api_delivery_partner_configs = array(
            [
                'delivery_partner_id' => '3',
                'configs' => array(
                    [
                        'request' => [
                            'api_url' => 'http://api.vintrans.com.vn:8989/api/customer/trackinglading',
                            'api_method' => 'GET',
                            'api_body_datas' => json_encode(array(
                                "Username" => "SGN0450BTN",
                                "Password" => "vts@2022"
                            )),
                            'api_delivery_code_field' => 'Code',
                        ],
                        'mapping' => [
                            'root_data_field' => '',
                            'is_root_string' => false,
                            'start_delivery_date' => 'MiningDay',
                            'complete_delivery_date' => 'FinishDate'
                        ],
                        'warehouse_code' => null,
                        'is_default' => true
                    ],
                )
            ],
            [
                'delivery_partner_id' => '4',
                'configs' => array(
                    [
                        'request' => [
                            'api_url' => 'https://api-v4-erp.vps.vn/api/V1Customer/APIC_spLadingGet',
                            'api_method' => 'GET',
                            'api_body_datas' => json_encode(array(
                                "CustomerCode" => "SGN14174SGN",
                                "AppAPIKey" => "0UtiJ3ZNav5HPf2jzdby4A=="
                            )),
                            'api_delivery_code_field' => 'LadingCode',
                        ],
                        'mapping' => [
                            'delivery_partner_id' => '4',
                            'root_data_field' => 'data',
                            'is_root_string' => true,
                            'start_delivery_date' => 'CreateDate',
                            'complete_delivery_date' => 'FinishDate'
                        ],
                        'warehouse_code' => null,
                        'is_default' => true
                    ],
                    [
                        'request' => [
                            'api_url' => 'https://api-v4-erp.vps.vn/api/V1Customer/APIC_spLadingGet',
                            'api_method' => 'GET',
                            'api_body_datas' => json_encode(array(
                                "CustomerCode" => "Ndh00068ndh",
                                "AppAPIKey" => "iOtY62qjuE4E5aRgaom1kw=="
                            )),
                            'api_delivery_code_field' => 'LadingCode',
                        ],
                        'mapping' => [
                            'delivery_partner_id' => '4',
                            'root_data_field' => 'data',
                            'is_root_string' => true,
                            'start_delivery_date' => 'CreateDate',
                            'complete_delivery_date' => 'FinishDate'
                        ],
                        'warehouse_code' => 3004,
                        'is_default' => false
                    ],
                    [
                        'request' => [
                            'api_url' => 'https://api-v4-erp.vps.vn/api/V1Customer/APIC_spLadingGet',
                            'api_method' => 'GET',
                            'api_body_datas' => json_encode(array(
                                "CustomerCode" => "Ndh00068ndh",
                                "AppAPIKey" => "iOtY62qjuE4E5aRgaom1kw=="
                            )),
                            'api_delivery_code_field' => 'LadingCode',
                        ],
                        'mapping' => [
                            'delivery_partner_id' => '4',
                            'root_data_field' => 'data',
                            'is_root_string' => true,
                            'start_delivery_date' => 'CreateDate',
                            'complete_delivery_date' => 'FinishDate'
                        ],
                        'warehouse_code' => 3007,
                        'is_default' => false
                    ],
                    [
                        'request' => [
                            'api_url' => 'https://api-v4-erp.vps.vn/api/V1Customer/APIC_spLadingGet',
                            'api_method' => 'GET',
                            'api_body_datas' => json_encode(array(
                                "CustomerCode" => "Ndh00068ndh",
                                "AppAPIKey" => "iOtY62qjuE4E5aRgaom1kw=="
                            )),
                            'api_delivery_code_field' => 'LadingCode',
                        ],
                        'mapping' => [
                            'delivery_partner_id' => '4',
                            'root_data_field' => 'data',
                            'is_root_string' => true,
                            'start_delivery_date' => 'CreateDate',
                            'complete_delivery_date' => 'FinishDate'
                        ],
                        'warehouse_code' => 3009,
                        'is_default' => false
                    ]
                )
            ]
        );
        try {
            DB::beginTransaction();
            foreach ($delivery_partners as $index => $delivery_partner) {
                $delivery_partner = DeliveryPartner::where('code', $delivery_partner['code'])->first();
                if (!$delivery_partner) {
                    $delivery_partner = DeliveryPartner::create($delivery_partner);
                }
                $api_delivery_partner_configs[$index]['delivery_partner_id'] = $delivery_partner->id;
                foreach ($api_delivery_partner_configs[$index]['configs'] as $config) {
                    $api_config = ApiDeliveryPartnerConfig::query()
                        ->where('delivery_partner_id', $delivery_partner->id)
                        ->where('warehouse_id', $config['warehouse_code'] ? Warehouse::where('code', $config['warehouse_code'])->first()->id : null)
                        ->whereHas('request', function ($query) use ($config) {
                            $query->where('api_url', $config['request']['api_url'])
                                ->where('api_method', $config['request']['api_method'])
                                // ->where('api_body_datas', $config['request']['api_body_datas'])
                                ->where('api_delivery_code_field', $config['request']['api_delivery_code_field']);
                        })
                        ->whereHas('mapping', function ($query) use ($config) {
                            $query->where('root_data_field', $config['mapping']['root_data_field'])
                                ->where('is_root_string', $config['mapping']['is_root_string'])
                                ->where('start_delivery_date', $config['mapping']['start_delivery_date'])
                                ->where('complete_delivery_date', $config['mapping']['complete_delivery_date']);
                        })
                        ->first();
                    if ($api_config) {
                        continue;
                    }

                    $config['delivery_partner_id'] = $delivery_partner->id;
                    $config['warehouse_id'] = $config['warehouse_code'] ? Warehouse::where('code', $config['warehouse_code'])->first()->id : null;
                    // $api_request = ApiDeliveryPartnerRequest::query()->where('api_url', $config['request']['api_url'])
                    //     ->where('api_method', $config['request']['api_method'])
                    //     // ->where('api_body_datas', $config['request']['api_body_datas'])
                    //     ->where('api_delivery_code_field', $config['request']['api_delivery_code_field'])
                    //     ->first();
                    $config['api_request_id'] = ApiDeliveryPartnerRequest::create($config['request'])->id;


                    // $api_mapping = ApiDeliveryPartnerMapping::where('root_data_field', $config['mapping']['root_data_field'])
                    //     ->where('is_root_string', $config['mapping']['is_root_string'])
                    //     ->where('start_delivery_date', $config['mapping']['start_delivery_date'])
                    //     ->where('complete_delivery_date', $config['mapping']['complete_delivery_date'])
                    //     ->first();
                    $config['api_mapping_id'] = ApiDeliveryPartnerMapping::create(array_merge($config['mapping'], ['delivery_partner_id' => $delivery_partner->id]))->id;
                    unset($config['request']);
                    unset($config['mapping']);
                    unset($config['warehouse_code']);
                    ApiDeliveryPartnerConfig::create($config);
                }
                // ApiDeliveryPartnerMapping::create($api_delivery_partner_mappings[$index]);
                DB::commit();
            }
        } catch (Exception $e) {
            Log::info($e);
            DB::rollBack();
        }


        $this->command->info('ExternalDeliveryPartnerSeeder has been completed!');
    }
}
