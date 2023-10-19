<?php

use App\Enums\Ai\Convert\ConvertMethod;
use App\Enums\Ai\Extract\CamelotFlavor;
use App\Enums\Ai\Extract\ExtractMethod;
use App\Enums\Ai\Restructure\RestructureMethod;
use App\Models\Business\ConvertTableConfig;
use App\Models\Business\ExtractDataConfig;
use App\Models\Business\ExtractOrder;
use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\RestructureDataConfig;
use App\Models\Master\CustomerGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract;

class ExtractOrderConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer_groups = array(
            array(
                'name' => 'Winmart',
            ),
            array(
                'name' => 'Mega',
            ),
            array(
                'name' => 'Lotte',
            ),
        );

        $extract_order_configs = array(
            array(
                'name' => 'Winmart mặc định',
                'reference_id' => null,
                'is_official' => true

            ),
            array(
                'name' => 'Mega mặc định',
                'reference_id' => null,
                'is_official' => true

            ),
            array(
                'name' => 'Lotte mặc định',
                'reference_id' => null,
                'is_official' => true
            )
        );
        $extract_data_configs = array(
            array(
                'method' => ExtractMethod::CAMELOT,
                'camelot_flavor' => CamelotFlavor::LATTICE,
                'is_merge_pages' => true,
                'exclude_head_tables_count' => 1,
                'exclude_tail_tables_count' => 0
            ),
            array(
                'method' => ExtractMethod::CAMELOT,
                'camelot_flavor' => CamelotFlavor::STREAM,
                'is_merge_pages' => true,
                'exclude_head_tables_count' => 0,
                'exclude_tail_tables_count' => 0,

            ),
            array(
                'method' => ExtractMethod::CAMELOT,
                'camelot_flavor' => CamelotFlavor::LATTICE,
                'is_merge_pages' => true,
                'exclude_head_tables_count' => 1,
                'exclude_tail_tables_count' => 0
            ),
        );

        $convert_table_configs = array(
            array(
                'method' => ConvertMethod::MANUAL,
                'manual_patterns' => json_encode(
                    [
                        [
                            "name" => "digits"
                        ],
                        [
                            "name" => "digits",
                            "length" => 8
                        ],
                        [
                            "name" => "digits"
                        ],
                        [
                            "name" => "digits"
                        ],
                        [
                            "name" => "words"
                        ],
                        [
                            "name" => "prices"
                        ],
                        [
                            "name" => "prices"
                        ],
                        [
                            "name" => "custom",
                            "pattern" => "[^\"]+"
                        ]
                    ]
                ),
                'regex_pattern' => null,
            ),
            array(
                'method' => ConvertMethod::LEAGUECSV,
                'manual_patterns' => null,
                'regex_pattern' => null,
            ),
            array(
                'method' => ConvertMethod::LEAGUECSV,
                'manual_patterns' => null,
                'regex_pattern' => null,
            ),
        );

        $restructure_data_configs = array(
            array(
                'method' => RestructureMethod::INDEXARRAYMAPPING,
                'structure' => json_encode(
                    [
                        "OrdUnit" => 5,
                        "ProductID" => 2,
                        "Quantity" => 4,
                        "ProductName" => 8
                    ]
                )
            ),
            array(
                'method' => RestructureMethod::KEYARRAYMAPPING,
                'structure' => json_encode(
                    [
                        "OrdUnit" => "PACK TYPE",
                        "ProductID" => "MM ARTICLE",
                        "Quantity" => "ORDER",
                        "ProductName" => "ARTICLE DESCRIPTION"
                    ]
                )
            ),
            array(
                'method' => RestructureMethod::KEYARRAYMAPPING,
                'structure' => json_encode(
                    [
                        "OrdUnit" => "Ord unit",
                        "ProductID" => "Prod cd",
                        "Quantity" => "Ord qty",
                        "ProductName" => "Prod nm"
                    ]
                )
            ),
        );

        foreach ($customer_groups as $index => $customer_group_data) {
            try {
                DB::beginTransaction();
                $customer_group = CustomerGroup::where('name', $customer_group_data['name'])->first();
                if (!$customer_group) {
                    $customer_group = CustomerGroup::create($customer_group_data);
                } else {
                    $old_extract_order_config = $customer_group->extract_order_configs->where('name', $extract_order_configs[$index]['name'])->first();
                    if ($old_extract_order_config) {
                        $old_extract_order_config->load('extract_data_config', 'convert_table_config', 'restructure_data_config');
                        $old_extract_data_config = $old_extract_order_config->extract_data_config;
                        $old_convert_table_config = $old_extract_order_config->convert_table_config;
                        $old_restructure_data_config = $old_extract_order_config->restructure_data_config;
                        // $old_extract_order_config->delete();
                        if (
                            $old_extract_data_config
                        ) {
                            $old_extract_data_config->fill($extract_data_configs[$index])->save();
                        }
                        if (
                            $old_convert_table_config
                        ) {
                            $old_convert_table_config->fill($convert_table_configs[$index])->save();
                        }
                        if (
                            $old_restructure_data_config
                        ) {
                            $old_restructure_data_config->fill($restructure_data_configs[$index])->save();
                        }
                    }
                    $this->command->info('ExtractOrderConfigSeeder completed');
                    return;
                }

                $extract_data_config = ExtractDataConfig::create($extract_data_configs[$index]);

                $convert_table_config = ConvertTableConfig::create($convert_table_configs[$index]);

                $restructure_data_config = RestructureDataConfig::create($restructure_data_configs[$index]);

                $extract_order_config = ExtractOrderConfig::create([
                    'name' => $extract_order_configs[$index]['name'],
                    'customer_group_id' => $customer_group->id,
                    'extract_data_config_id' => $extract_data_config->id,
                    'convert_table_config_id' => $convert_table_config->id,
                    'restructure_data_config_id' => $restructure_data_config->id,
                ]);


                DB::commit();
            } catch (\Exception $exception) {
                DB::rollback();
                error_log($exception);
            }
        }
        $this->command->info('ExtractOrderConfigSeeder completed');
    }
}
