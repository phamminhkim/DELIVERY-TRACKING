<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerGroup;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapMaterialMapping;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;




class SapMaterialMappingRepository extends RepositoryAbs
{
    public function createSapMateriasMappingsFromExcel()
    {
        try {
            $validator = Validator::make($this->data, [
                'file' => 'required|mimes:xlsx,xls',
            ], [
                'file.required' => 'File là bắt buộc.',
                'file.mimes' => 'File không đúng định dạng.',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $file = $this->request->file('file');
                $excel_extractor = new ExcelExtractor();
                $raw_table_data = $excel_extractor->extractData($file);
                $template_structure = [
                    'customer_group_name' => 0,
                    'customer_material_sku_code' => 1,
                    'customer_material_name' => 2,
                    'customer_material_unit' => 4,
                    'sap_material_code' => 5,
                    'sap_material_unit' => 8,
                    'percentage' => 9
                ];
                $mapping_table = $excel_extractor->structureData($raw_table_data, $template_structure);
                $result = collect([]);
                DB::beginTransaction();
                foreach ($mapping_table as $material) {
                    $customer_group = CustomerGroup::query()
                        ->where('name', $material['customer_group_name'])
                        ->first();
                    if (!$customer_group) {
                        $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $material['customer_group_name'];
                        continue;
                    }
                    $customer_material_existed = CustomerMaterial::query()
                        ->whereHas('customer_group', function ($query) use ($customer_group) {
                            return $query->where('customer_group_id', $customer_group->id);
                        })
                        ->where('customer_sku_code', $material['customer_material_sku_code'])
                        ->first();
                    if ($customer_material_existed) {
                        $customer_material = $customer_material_existed;
                    } else {
                        $customer_material = CustomerMaterial::create([
                            'customer_group_id' => $customer_group->id,
                            'customer_sku_code' => $material['customer_material_sku_code'],
                            'customer_sku_name' => $material['customer_material_name'],
                            'customer_sku_unit' => $material['customer_material_unit']
                        ]);
                    }

                    $sap_material = SapMaterial::query()
                        ->where('sap_code', $material['sap_material_code'])
                        ->whereHas('unit', function ($query) use ($material) {
                            return $query->where('unit_code', $material['sap_material_unit']);
                        })
                        ->first();
                    if (!$sap_material) {
                        $this->errors[] = 'Không tìm thấy mã hàng sap (' . $material['sap_material_code'] . ') với đơn vị tính (' . $material['sap_material_unit'] . ')';
                        // $exist_sap_material = SapMaterial::query()
                        //     ->where('sap_code', $material['sap_material_code'])
                        //     ->first();
                        // $unit = SapUnit::query()->where('unit_code', $material['sap_material_unit'])->first();
                        // $exist_sap_material->unit_id = $unit->id;
                        // SapMaterial::create($exist_sap_material->toArray());
                        continue;
                    }

                    if ($customer_material->sap_material_id) {
                        $this->errors[] = 'Mã hàng khách hàng ' . $material['customer_material_sku_code'] . ' đã được map với mã hàng sap ' . $customer_material->sap_material->sap_code;
                        continue;
                    }
                    if (
                        SapMaterialMapping::query()
                        ->where('customer_material_id', $customer_material->id)
                        ->where('sap_material_id', $sap_material->id)
                        ->where('percentage', $material['percentage'])
                        ->first()
                    ) {
                        $this->errors[] = 'Mã hàng khách hàng ' . $material['customer_material_sku_code'] . ' đã được map với mã hàng sap ' . $sap_material->sap_code . ' với tỉ lệ ' . $material['percentage'];
                        continue;
                    }
                    $sap_material_mapping = SapMaterialMapping::create([
                        'customer_material_id' => $customer_material->id,
                        'sap_material_id' => $sap_material->id,
                        'percentage' => $material['percentage']
                    ]);
                    $result->push($sap_material_mapping);
                }
                DB::commit();
                return array(
                    "created_list" => $result,
                    "errors" => $this->errors
                );
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function getAvailableSapMaterialMappings()
    {
        try {
            $query = SapMaterialMapping::query();

            if ($this->request->filled('search')) {
                $query->search($this->request->search);
                $query->limit(200);
            }

            if ($this->request->filled('customer_material_ids')) {
                $customer_material_ids = $this->request->customer_material_ids;
                $query->whereIn('customer_material_id', $customer_material_ids);
            }

            if ($this->request->filled('sap_material_ids')) {
                $sap_material_ids = $this->request->sap_material_ids;
                $query->whereIn('sap_material_id', $sap_material_ids);
            }

            $query->with([
                'customer_material' => function ($query) {
                    $query->select(['id', 'customer_group_id', 'customer_sku_code', 'customer_sku_name', 'customer_sku_unit']);
                    $query->with('customer_group:id,name'); // Lấy thông tin cột "name" từ bảng "customer_group"
                },
                'sap_material' => function ($query) {
                    $query->select(['id', 'sap_code', 'unit_id', 'name']);
                    $query->with('unit:id,unit_code'); // Lấy thông tin cột "name" từ bảng "sap_unit"
                },
            ]);

            $sapMaterialMappings = $query->get();

            return $sapMaterialMappings;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewSapMaterialMappings($customerMaterialData)
{
    try {
        $validator = Validator::make($customerMaterialData, [
            'customer_material_id' => 'nullable|integer|exists:customer_materials,id',
            'sap_material_id' => 'required|integer|exists:sap_materials,id',
            'percentage' => 'required|integer',
            'customer_group_id' => 'nullable',
            'customer_sku_code' => 'nullable|unique:customer_materials,customer_sku_code',
            'customer_sku_name' => 'nullable',
            'customer_sku_unit' => 'nullable',
        ], [
            'customer_material_id.integer' => 'Mã SKU khách hàng phải là số nguyên.',
            'sap_material_id.required' => 'Yêu cầu nhập mã đối chiếu.',
            'sap_material_id.integer' => 'Mã đối chiếu phải là số nguyên.',
            'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
            'percentage.required' => 'Yêu cầu nhập tỉ lệ sản phẩm.',
            'percentage.integer' => 'Tỉ lệ sản phẩm phải là số nguyên.',
            'customer_sku_code.required' => 'Yêu cầu nhập mã SKU khách hàng.',
            'customer_sku_code.unique' => 'Mã SKU khách hàng đã tồn tại.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($this->data as $sap_material_mapping => $validator) {
                if ($errors->has($sap_material_mapping)) {
                    $this->errors[$sap_material_mapping] = $errors->first($sap_material_mapping);
                    return false;
                }
            }
        }

        $sap_material = SapMaterial::find($customerMaterialData['sap_material_id']);
        if (!$sap_material) {
            $this->errors[] = 'Không tìm thấy mã đối chiếu sản phẩm ' . $customerMaterialData['sap_material_id'];
            return false;
        }

        $customer_group = CustomerGroup::find($customerMaterialData['customer_group_id']);
        $customer_group_id = $customerMaterialData['customer_group_id'] ?? null;
        if (!$customer_group) {
            $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customerMaterialData['customer_group_id'];
            return false;
        }

        $customer_sku_code = $customerMaterialData['customer_sku_code'] ?? '';

        $customer_material_existed = CustomerMaterial::where('customer_group_id', $customer_group->id)
            ->where('customer_sku_code', $customer_sku_code)
            ->first();

        if ($customer_material_existed && !$customerMaterialData['customer_material_id']) {
            $customer_material = $customer_material_existed;
            $customer_material->fill([
                'customer_sku_code' => $customer_sku_code,
                'customer_sku_name' => $customerMaterialData['customer_sku_name'] ?? '',
                'customer_sku_unit' => $customerMaterialData['customer_sku_unit'] ?? '',
            ]);
            $customer_material->save();
        } elseif (!$customer_material_existed) {
            $customer_material = CustomerMaterial::create([
                'customer_group_id' => $customer_group_id,
                'customer_sku_code' => $customer_sku_code,
                'customer_sku_name' => $customerMaterialData['customer_sku_name'] ?? '',
                'customer_sku_unit' => $customerMaterialData['customer_sku_unit'] ?? '',
            ]);
        }

        $customer_material_id = $customerMaterialData['customer_material_id'] ?? null;

        $existing_mapping = SapMaterialMapping::where('customer_material_id', $customer_material_id)
            ->where('sap_material_id', $customerMaterialData['sap_material_id'])
            ->exists();

        if ($existing_mapping) {
            $this->errors[] = 'Mapping dữ liệu đã tồn tại.';
            return false;
        }

        $sap_material_mapping = SapMaterialMapping::create([
            'customer_material_id' => $customer_material_id ?? '',
            'sap_material_id' => $customerMaterialData['sap_material_id'],
            'percentage' => $customerMaterialData['percentage'],
        ]);

        return $sap_material_mapping;
    } catch (\Exception $exception) {
        $this->message = $exception->getMessage();
        $this->errors = $exception->getTrace();
        return false;
    }
}
    public function updateOrInsert()
    {
        $validator = Validator::make($this->data, [
            // '*.company_code' => 'required|string|exists:companies,code',
            '*.code' => 'required|string',
            '*.name' => 'required|string',
        ], [
            // '*.company_code.required' => 'Yêu cầu nhập mã công ty.',
            // '*.company_code.string' => 'Mã công ty phải là chuỗi.',
            // '*.company_code.exists' => 'Mã công ty không tồn tại.',
            '*.code.required' => 'Yêu cầu nhập mã material.',
            '*.code.string' => 'Mã material phải là chuỗi.',
            '*.name.required' => 'Yêu cầu nhập tên material.',
            '*.name.string' => 'Tên material phải là chuỗi.',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
            return $this->errors;
        } else {
            try {
                $result = array(
                    'insert_count' => 0,
                    'update_count' => 0,
                    'skip_count' => 0,
                    'delete_count' => 0,
                    'error_count' => 0,
                );
                foreach ($this->data as $material) {
                    // $company_id = '';
                    $unit = SapUnit::where('unit_code', $material['unit_code'])->first();
                    if (!$unit) {
                        $unit = SapUnit::create(['unit_code' => $material['unit_code']]);
                    }
                    // $company = Company::where('code', $material['company_code'])->first();

                    // if (!$company) {
                    //     $result['skip_count']++;
                    //     continue;
                    // }
                    $exist_sap_material = SapMaterial::query()
                        // ->where('company_id',  $company->code)
                        ->where('sap_code', $material['code'])
                        ->where('unit_id', $unit->id)->first();

                    if ($material['status'] == "delete") {
                        $exist_sap_material->is_deleted = true;
                        $exist_sap_material->save();
                    } {
                        if ($exist_sap_material) {
                            $exist_sap_material->update([
                                'name' => $material['name'],
                            ]);
                            $result['update_count']++;
                        } else {

                            SapMaterial::create([
                                // 'company_id' => $company->code,
                                'unit_id' => $unit->id,
                                'sap_code' => $material['code'],
                                'name' => $material['name'],
                            ]);
                            $result['insert_count']++;
                        }
                    }
                }
                return $result;
            } catch (\Exception $exception) {
                $this->message = $exception->getMessage();
                $this->errors = $exception->getTrace();
            }
        }
    }
    public function updateSapMaterialMapping($id, $customerMaterialData)
    {
        try {
            $validator = Validator::make($customerMaterialData, [
                'sap_material_id' => 'integer|exists:sap_materials,id',
                'percentage' => 'integer',
                'customer_group_id' => '',
                'customer_sku_code' => '',
                'customer_sku_name' => '',
                'customer_sku_unit' => '',
            ], [
                'sap_material_id.integer' => 'Mã đối chiếu phải là số nguyên.',
                'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
                'percentage.integer' => 'Tỉ lệ sản phẩm phải là số nguyên.',
                // 'customer_sku_code.unique' => 'Mã SKU khách hàng đã tồn tại.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $this->errors = $errors->all();
                return false;
            }

            $sap_material_mapping = SapMaterialMapping::find($id);
            if (!$sap_material_mapping) {
                $this->errors[] = 'Không tìm thấy đối chiếu sản phẩm.';
                return false;
            }

            $sap_material = SapMaterial::find($customerMaterialData['sap_material_id']);
            if (!$sap_material) {
                $this->errors[] = 'Không tìm thấy mã đối chiếu sản phẩm ' . $customerMaterialData['sap_material_id'];
                return false;
            }

            $customer_material = CustomerMaterial::find($sap_material_mapping->customer_material_id);
            if (!$customer_material) {
                $this->errors[] = 'Không tìm thấy đối chiếu khách hàng.';
                return false;
            }

            $customer_group = CustomerGroup::find($customerMaterialData['customer_group_id']);
            if (!$customer_group) {
                $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customerMaterialData['customer_group_id'];
                return false;
            }

            // Bắt đầu giao dịch
            DB::beginTransaction();

            try {
                $customer_material->customer_group_id = $customerMaterialData['customer_group_id'];
                $customer_material->customer_sku_code = $customerMaterialData['customer_sku_code'];
                $customer_material->customer_sku_name = $customerMaterialData['customer_sku_name'];
                $customer_material->customer_sku_unit = $customerMaterialData['customer_sku_unit'];
                $customer_material->save();

                $sap_material_mapping->sap_material_id = $customerMaterialData['sap_material_id'];
                $sap_material_mapping->percentage = $customerMaterialData['percentage'];
                $sap_material_mapping->save();

                // Hoàn thành giao dịch
                DB::commit();

                return $sap_material_mapping;
            } catch (\Exception $exception) {
                // Lỗi xảy ra, hủy giao dịch
                DB::rollBack();

                $this->message = $exception->getMessage();
                $this->errors = $exception->getTrace();
                return false;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function deleteExistingSapMaterialMapping($id)
    {
        try {
            $sapMaterialMapping = SapMaterialMapping::findOrFail($id);
            $sapMaterialMapping->delete();
            return $sapMaterialMapping;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
