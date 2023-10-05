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
        );

        $restructure_data_configs = array(
            array(
                'method' => RestructureMethod::INDEXARRAYMAPPING,
                'structure' => json_encode(
                    [
                        "Barcode" => 3,
                        "Unit" => 5,
                        "ProductID" => 2,
                        "Quantity" => 4,
                        "UnitPrice" => 6,
                        "Amount" => 7,
                        "Description" => 8
                    ]
                )
            ),
            array(
                'method' => RestructureMethod::KEYARRAYMAPPING,
                'structure' => json_encode(
                    [
                        "Unit" => "PACK TYPE",
                        "ProductID" => "MM ARTICLE",
                        "Quantity" => "ORDER",
                        "name" => "ARTICLE DESCRIPTION"
                    ]
                )
            ),
        );

        foreach ($customer_groups as $index => $customer_group) {
            try {
                DB::beginTransaction();
                if (!CustomerGroup::where('name', $customer_group['name'])->exists()) {
                    $customer_group = CustomerGroup::create($customer_group);
                } else {
                    throw new \Exception('Customer group already exists');
                }
                $extract_data_config = ExtractDataConfig::create($extract_data_configs[$index]);
                $convert_table_config = ConvertTableConfig::create($convert_table_configs[$index]);
                $restructure_data_config = RestructureDataConfig::create($restructure_data_configs[$index]);

                $extract_order_config = ExtractOrderConfig::create([
                    'customer_group_id' => $customer_group->id,
                    'extract_data_config_id' => $extract_data_config->id,
                    'convert_table_config_id' => $convert_table_config->id,
                    'restructure_data_config_id' => $restructure_data_config->id,
                ]);

                $count = CustomerGroup::query()
                    ->where('name', $customer_group->name)
                    ->whereHas('extract_data_config', function ($query) use ($extract_data_config) {
                        $query->where('method', $extract_data_config->method)
                            ->where('camelot_flavor', $extract_data_config->camelot_flavor)
                            ->where('is_merge_pages', $extract_data_config->is_merge_pages)
                            ->where('exclude_head_tables_count', $extract_data_config->exclude_head_tables_count)
                            ->where('exclude_tail_tables_count', $extract_data_config->exclude_tail_tables_count);
                    })
                    ->whereHas('convert_table_config', function ($query) use ($convert_table_config) {
                        $query->where('method', $convert_table_config->method)
                            ->where('manual_patterns', $convert_table_config->manual_patterns)
                            ->where('regex_pattern', $convert_table_config->regex_pattern);
                    })
                    ->whereHas('restructure_data_config', function ($query) use ($restructure_data_config) {
                        $query->where('method', $restructure_data_config->method)
                            ->where('structure', $restructure_data_config->structure);
                    })
                    ->count();

                if ($count > 1) {
                    throw new \Exception('Extract order config already exists');
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollback();
                error_log($exception);
            }
        }
        $this->command->info('ExtractOrderConfigSeeder completed');
    }
}
