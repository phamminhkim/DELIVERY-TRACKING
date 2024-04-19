<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialCategoryType;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class MaterialCategoryTypeRepository extends RepositoryAbs
{
    public function getAvailableCategoryTypes()
    {
        try {
            $materialCategoryTypes = MaterialCategoryType::orderBy('id', 'desc')
                ->get();

                return [
                    'success' => true,
                    'items' => $materialCategoryTypes
                ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewCategoryType()
    {

        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:material_category_types,code',
                'name' => 'required|string',
            ], [
                'code.required' => 'Không được để mã trống.',
                'code.string' => 'Mã công ty phải là chuỗi.',
                'code.unique' => 'Mã đã tồn tại.',
                'name.required' => 'Không được để tên trống.',
                'name.string' => 'Tên phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $material_category_type = MaterialCategoryType::create($this->data);

                // return $material_category_type;
                return [
                    'success' => true,
                    'items' => $material_category_type
                ];
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateExistingCategoryType($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:material_category_types,code,' . $id,
                'name' => 'required|string',
            ], [
                'code.required' => 'Không được để mã trống.',
                'code.string' => 'Mã công ty phải là chuỗi.',
                'code.unique' => 'Mã đã tồn tại.',
                'name.required' => 'Không được để tên trống.',
                'name.string' => 'Tên phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $material_category_type = MaterialCategoryType::find($id);
                $material_category_type->update($this->data);

                return [
                    'success' => true,
                    'items' => $material_category_type
                ];
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function deleteExistingCategoryType($id)
    {
        try {
            $material_category_type = MaterialCategoryType::find($id);
            $material_category_type->delete();

            return [
                'success' => true,
                'items' => $material_category_type
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
