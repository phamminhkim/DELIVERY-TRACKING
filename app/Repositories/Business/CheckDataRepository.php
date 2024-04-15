<?php

namespace App\Repositories\Business;

use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\CustomerGroup;
use App\Models\Master\SapMaterialMapping;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Implementations\Files\LocalFileService;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CheckDataRepository extends RepositoryAbs
{
    public function checkMaterialSAP()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required|exists:customer_groups,id',
                'customer_sku_code' => 'required',
                'customer_sku_unit' => 'required'
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $customer_group_id = $this->data['customer_group_id'];
            $customer_sku_code = $this->data['customer_sku_code'];
            $customer_sku_unit = $this->data['customer_sku_unit'];

            // Lấy thông tin nhóm khách hàng từ bảng customer_groups
            $customerGroup = CustomerGroup::find($customer_group_id);

            if (!$customerGroup) {
                $this->message = 'Invalid customer group';
                return false;
            }

            $customerMaterials = CustomerMaterial::where('customer_group_id', $customer_group_id)
                ->where('customer_sku_code', $customer_sku_code)
                ->where('customer_sku_unit', $customer_sku_unit)
                ->get();

            $mappingData = [];
            $unmappedData = [];

            foreach ($customerMaterials as $customerMaterial) {
                $customer_sku_code = $customerMaterial->customer_sku_code;
                $customer_sku_unit = $customerMaterial->customer_sku_unit;

                // Tìm mã trong bảng SapMaterial
                $sapMaterial = SapMaterial::where('bar_code', $customer_sku_code)->first();

                if ($sapMaterial) {
                    // Thêm thông tin vào mappingData
                    $sap_code = $sapMaterial->sap_code;
                    $bar_code = $sapMaterial->bar_code;
                    $unit_code = $sapMaterial->unit_code;
                    $name = $sapMaterial->name;
                    $unit_id = $sapMaterial->unit_id;

                    $sapUnit = SapUnit::find($unit_id);
                    if ($sapUnit) {
                        $unit_code = $sapUnit->unit_code;
                    } else {
                        $unit_code = null; // Xử lý trường hợp unit không tồn tại
                    }

                    $mappingData[] = [
                        'customer_sku_code' => $customer_sku_code,
                        'customer_sku_unit' => $customer_sku_unit,
                        'bar_code' => $bar_code,
                        'sap_code' => $sap_code,
                        'unit_id'  => $unit_id,
                        'name' => $name,
                        'unit_code' => $unit_code,
                    ];
                } else {
                    $sapMaterialMappings = SapMaterialMapping::whereHas('customer_material', function ($query) use ($customer_group_id, $customer_sku_code) {
                        $query->where('customer_group_id', $customer_group_id)
                            ->where('customer_sku_code', $customer_sku_code);
                    })->get();

                    foreach ($sapMaterialMappings as $sapMaterialMapping) {
                        // Lấy thông tin từ bảng CustomerMaterial
                        $customer_sku_code = $sapMaterialMapping->customer_material->customer_sku_code;
                        $customer_sku_unit = $sapMaterialMapping->customer_material->customer_sku_unit;

                        $sap_material_id = $sapMaterialMapping->sap_material_id;
                        $customer_material_id = $sapMaterialMapping->customer_material_id;

                        $sapMaterial = SapMaterial::find($sap_material_id);
                        $customerMaterial = CustomerMaterial::find($customer_material_id);

                        if ($sapMaterial) {
                            // Thêm thông tin vào mappingData
                            $sap_code = $sapMaterial->sap_code;
                            $bar_code = $sapMaterial->bar_code;
                            $unit_code = $sapMaterial->unit_code;
                            $name = $sapMaterial->name;
                            $unit_id = $sapMaterial->unit_id;

                            $sapUnit = SapUnit::find($unit_id);
                            if ($sapUnit) {
                                $unit_code = $sapUnit->unit_code;
                            } else {
                                $unit_code = null; // Xử lý trường hợp unit không tồn tại
                            }

                            $mappingData[] = [
                                'customer_sku_code' => $customer_sku_code,
                                'customer_sku_unit' => $customer_sku_unit,
                                'bar_code' => $bar_code,
                                'sap_code' => $sap_code,
                                'unit_id'  => $unit_id,
                                'name' => $name,
                                'unit_code' => $unit_code,
                            ];
                        }
                    }
                }
            }
            // Thêm vào unmappedData
            $unmappedData[] = [
                'customer_sku_code' => $customer_sku_code,
                'customer_sku_unit' => $customer_sku_unit,
            ];

            return response()->json([
                'mappingData' => $mappingData ?? [],
                'unmappedData' => $unmappedData ?? [],
            ]);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
