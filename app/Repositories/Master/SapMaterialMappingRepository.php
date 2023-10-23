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


    public function getAvailableSapMaterialMappings($request)
    {
        try {
            $result = array();
            $query = SapMaterialMapping::query();
            $sapMaterialMappings = $query->paginate($request->per_page, ['*'], 'page', $request->page);
            $result['sap_material_mappings'] = $sapMaterialMappings->items();
            $result['paginate'] = [
                'current_page' => $sapMaterialMappings->currentPage(),
                'last_page' => $sapMaterialMappings->lastPage(),
                'total' => $sapMaterialMappings->total(),
            ];
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewSapMaterialMappings()
    {
        try {
            $validator = Validator::make($this->data, [
                // 'company_id' => 'required|integer|exists:companies,code',
                'customer_material_id' => 'required|string|exists:customer_materials,id',
                'sap_material_id' => 'required|string|exists:sap_materials,id',
                'percentage' => 'required|integer',
            ], [
                'customer_material_id.required' => 'Yêu cầu nhập mã sản phẩm khách hàng.',
                'customer_material_id.string' => 'Mã sản phẩm khách hàng phải là chuỗi.',
                'customer_material_id.exists' => 'Mã sản phẩm khách hàng đã tồn tại.',
                'sap_material_id.string' => 'Mã đối chiếu phải là chuỗi.',
                'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
                'percentage.required' => 'Yêu cầu nhập tỉ lệ sản phẩm.',
                'percentage.integer' => 'Tỉ lệ sản phẩm phải là số nguyên.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $sapMaterialMapping => $validator) {
                    if ($errors->has($sapMaterialMapping)) {
                        $this->errors[$sapMaterialMapping] = $errors->first($sapMaterialMapping);
                        return false;
                    }
                }
            } else {

                $customer_material = CustomerMaterial::find($this->data['customer_material_id']);
                if (!$customer_material) {
                    $this->errors = 'Không tìm thấy mã sản phẩm khách hàng ' . $this->data['customer_material_id'];
                    return false;
                }

                $sap_material = SapMaterial::find($this->data['sap_material_id']);
                if (!$sap_material) {
                    $this->errors = 'Không tìm thấy mã đối chiếu sản phẩm ' . $this->data['sap_material_id'];
                    return false;
                }
                $this->data['sap_material_id'] = $sap_material->id ?? null;

                $sapMaterialMapping = SapMaterialMapping::create([
                    'customer_material_id' => $this->data['customer_material_id'],
                    'sap_material_id' => $this->data['sap_material_id'],
                    'percentage' => $this->data['name'],
                ]);

                return $sapMaterialMapping;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    // public function updateOrInsert()
    // {
    //     $validator = Validator::make($this->data, [
    //         // '*.company_code' => 'required|string|exists:companies,code',
    //         '*.code' => 'required|string',
    //         '*.name' => 'required|string',
    //     ], [
    //         // '*.company_code.required' => 'Yêu cầu nhập mã công ty.',
    //         // '*.company_code.string' => 'Mã công ty phải là chuỗi.',
    //         // '*.company_code.exists' => 'Mã công ty không tồn tại.',
    //         '*.code.required' => 'Yêu cầu nhập mã material.',
    //         '*.code.string' => 'Mã material phải là chuỗi.',
    //         '*.name.required' => 'Yêu cầu nhập tên material.',
    //         '*.name.string' => 'Tên material phải là chuỗi.',
    //     ]);

    //     if ($validator->fails()) {
    //         $this->errors = $validator->errors()->all();
    //         return $this->errors;
    //     } else {
    //         try {
    //             $result = array(
    //                 'insert_count' => 0,
    //                 'update_count' => 0,
    //                 'skip_count' => 0,
    //                 'delete_count' => 0,
    //                 'error_count' => 0,
    //             );
    //             foreach ($this->data as $material) {
    //                 // $company_id = '';
    //                 $unit = SapUnit::where('unit_code', $material['unit_code'])->first();
    //                 if (!$unit) {
    //                     $unit = SapUnit::create(['unit_code' => $material['unit_code']]);
    //                 }
    //                 // $company = Company::where('code', $material['company_code'])->first();

    //                 // if (!$company) {
    //                 //     $result['skip_count']++;
    //                 //     continue;
    //                 // }
    //                 $exist_sap_material = SapMaterial::query()
    //                     // ->where('company_id',  $company->code)
    //                     ->where('sap_code', $material['code'])
    //                     ->where('unit_id', $unit->id)->first();

    //                 if ($material['status'] == "delete") {
    //                     $exist_sap_material->is_deleted = true;
    //                     $exist_sap_material->save();
    //                 } {
    //                     if ($exist_sap_material) {
    //                         $exist_sap_material->update([
    //                             'name' => $material['name'],
    //                         ]);
    //                         $result['update_count']++;
    //                     } else {

    //                         SapMaterial::create([
    //                             // 'company_id' => $company->code,
    //                             'unit_id' => $unit->id,
    //                             'sap_code' => $material['code'],
    //                             'name' => $material['name'],
    //                         ]);
    //                         $result['insert_count']++;
    //                     }
    //                 }
    //             }
    //             return $result;
    //         } catch (\Exception $exception) {
    //             $this->message = $exception->getMessage();
    //             $this->errors = $exception->getTrace();
    //         }
    //     }
    // }
    // public function updateExistingSapMaterial($id)
    // {
    //     try {
    //         $validator = Validator::make($this->data, [
    //             // 'company_id' => 'required|integer|exists:companies,code',
    //             'sap_code' => 'required|string:sap_materials,sap_code,',
    //             'unit_id' => 'required|integer|exists:sap_units,id',
    //             'name' => 'required|string',
    //         ], [
    //             // 'company_id.required' => 'Yêu cầu nhập mã công ty.',
    //             // 'company_id.integer' => 'Mã công ty phải là chuỗi.',
    //             // 'company_id.exists' => 'Mã công ty không tồn tại.',
    //             'sap_code.required' => 'Yêu cầu nhập mã material.',
    //             'sap_code.string' => 'Mã công ty phải là chuỗi.',
    //             //'sap_code.unique' => 'Mã material đã tồn tại.',
    //             //'unit_id.integer' => 'Mã material phải là chuỗi.',
    //             'unit_id.exists' => 'Mã unit không tồn tại.',
    //             'name.required' => 'Yêu cầu nhập tên material.',
    //             'name.string' => 'Tên material phải là chuỗi.',
    //         ]);

    //         if ($validator->fails()) {
    //             $errors = $validator->errors();
    //             foreach ($this->data as $SapMaterial => $validator) {
    //                 if ($errors->has($SapMaterial)) {
    //                     $this->errors[$SapMaterial] = $errors->first($SapMaterial);
    //                     return false;
    //                 }
    //             }
    //         } else {
    //             // $company = Company::where('code', $this->data['company_id'])->first();
    //             // if (!$company) {
    //             //     $this->errors = 'Không tìm thấy công ty có mã ' . $this->data['company_id'];
    //             //     return false;
    //             // }

    //             $sap_unit = SapUnit::find($this->data['unit_id']);
    //             if (!$sap_unit) {
    //                 $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
    //                 return false;
    //             }

    //             $sapMaterial = SapMaterial::findOrFail($id);
    //             $sapMaterial->fill([
    //                 // 'company_id' => $this->data['company_id'],
    //                 'sap_code' => $this->data['sap_code'],
    //                 'unit_id' => $this->data['unit_id'],
    //                 'name' => $this->data['name'],
    //             ]);
    //             $sapMaterial->save();

    //             return $sapMaterial;
    //         }
    //     } catch (\Exception $exception) {
    //         $this->message = $exception->getMessage();
    //         $this->errors = $exception->getTrace();
    //     }
    // }
    // public function deleteExistingSapMaterial($id)
    // {
    //     try {
    //         $sapMaterial = SapMaterial::findOrFail($id);
    //         $sapMaterial->delete();
    //         return $sapMaterial;
    //     } catch (\Exception $exception) {
    //         $this->message = $exception->getMessage();
    //         $this->errors = $exception->getTrace();
    //     }
    // }

}
