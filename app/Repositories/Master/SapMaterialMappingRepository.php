<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerGroup;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapMaterialMapping;
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
                    'customer_material_po_code' => 1,
                    'customer_material_name' => 2,
                    'customer_material_unit' => 4,
                    'sap_material_code' => 5,
                    'percentage' => 9
                ];
                $mapping_table = $excel_extractor->structureData($raw_table_data, $template_structure);
                $result = collect([]);
                DB::beginTransaction();
                foreach ($mapping_table as $material) {
                    $customer_group = CustomerGroup::where('name', $material['customer_group_name'])->first();
                    if (!$customer_group) {
                        $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $material['customer_group_name'];
                        continue;
                    }
                    $customer_material_existed = CustomerMaterial::query()
                        ->whereHas('customer_group', function ($query) use ($customer_group) {
                            return $query->where('customer_group_id', $customer_group->id);
                        })
                        ->where('po_code', $material['customer_material_po_code'])
                        ->first();
                    if ($customer_material_existed) {
                        $customer_material = $customer_material_existed;
                    } else {
                        $customer_material = CustomerMaterial::create([
                            'customer_group_id' => $customer_group->id,
                            'po_code' => $material['customer_material_po_code'],
                            'name' => $material['customer_material_name'],
                            'unit' => $material['customer_material_unit']
                        ]);
                    }

                    $sap_material = SapMaterial::where('sap_code', $material['sap_material_code'])->first();
                    if (!$sap_material) {
                        $this->errors[] = 'Không tìm thấy mã hàng sap' . $material['sap_material_code'];
                        continue;
                    }

                    $sap_material_mapping = SapMaterialMapping::create([
                        'customer_material_id' => $customer_material->id,
                        'sap_material_id' => $sap_material->id,
                        'percentage' => $material['percentage']
                    ]);
                    $result->push($sap_material_mapping);
                }
                if (count($this->errors ?? []) > 0) {
                    return false;
                }
                DB::commit();
                return $result;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
