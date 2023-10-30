<?php

use Illuminate\Database\Seeder;
use App\Models\Business\ExtractError;


class ExtractErrorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $extract_errors = [
            [
                'code' => '10',
                'name' => 'EXTRACT_ERROR',
                'is_has_log' => false,
            ],
            [
                'code' => '20',
                'name' => 'CONVERT_ERROR',
                'is_has_log' => false,
            ],
            [
                'code' => '30',
                'name' => 'RESTRUCTURE_ERROR',
                'is_has_log' => false,
            ],
            [
                'code' => '40',
                'name' => 'NOT_FOUND_CUSTOMER_MATERIAL',
                'is_has_log' => true,
            ],
            [
                'code' => '50',
                'name' => 'NOT_FOUND_SAP_MATERIAL',
                'is_has_log' => true,
            ],
        ];

        foreach ($extract_errors as $extract_error) {
            $existing_error = ExtractError::where('code', $extract_error['code'])->first();

            if ($existing_error) {
                $existing_error->name = $extract_error['name'];
                $existing_error->save();
            } else {
                ExtractError::create($extract_error);
            }
        }
        $this->command->info('ExtractErrorsSeeder has been completed!');
    }
}
