<?php

use App\Models\Master\MaterialCategoryType;
use Illuminate\Database\Seeder;

class MaterialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $material_category_types = [
            [
                'name' => 'Combo',
                'is_deleted' => false,
            ],
            [
                'name' => 'ExtraOffer',
                'is_deleted' => false,
            ],
        ];

        foreach ($material_category_types as $material_category_type) {
            $existing_category_type = MaterialCategoryType::where('name', $material_category_type['name'])->first();

            if ($existing_category_type) {
                $existing_category_type->update([
                    'is_delete' => $material_category_type['is_delete'],
                ]);
            } else {
                MaterialCategoryType::create($material_category_type);
            }
        }

        $this->command->info('MaterialCategorySeeder has been completed!');
    }
}
