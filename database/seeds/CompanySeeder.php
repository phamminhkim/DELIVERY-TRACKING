<?php

use App\Models\Master\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create some companies
        $companies = [
            [
                'code' => '1000',
                'name' => 'TLG',
                'is_active' => true,
            ],
            [
                'code' => '2000',
                'name' => 'TLLT',
                'is_active' => true,
            ],
            [
                'code' => '3000',
                'name' => 'TLHC',
                'is_active' => true,
            ],
            [
                'code' => '5000',
                'name' => 'NTL',
                'is_active' => true,
            ],
            [
                'code' => '7000',
                'name' => 'CLEW',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            if (!Company::where('code', $company['code'])->exists())
                Company::create($company);
        }
    }
}
