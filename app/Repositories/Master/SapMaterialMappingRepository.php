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
}
