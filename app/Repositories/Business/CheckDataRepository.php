<?php

namespace App\Repositories\Business;

use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\CustomerGroup;
use App\Models\Master\SapMaterialMapping;
use App\Models\Master\SapUnit;
use App\Services\Excel\ExcelExtractor;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Implementations\Files\LocalFileService;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CheckDataRepository extends RepositoryAbs
{
    protected $file_service;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->file_service = new LocalFileService();
    }
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

            // Get customer group information from the customer_groups table
            $customerGroup = CustomerGroup::find($customer_group_id);

            if (!$customerGroup) {
                $this->message = 'Không tìm thấy nhóm khách hàng';
                return false;
            }

            $customerMaterials = CustomerMaterial::where('customer_group_id', $customer_group_id)
                ->where('customer_sku_code', $customer_sku_code)
                ->where('customer_sku_unit', $customer_sku_unit)
                ->get();

            $mappingData = [];

            foreach ($customerMaterials as $customerMaterial) {
                $customer_group_id = $customerMaterial->customer_group_id;
                $customer_sku_code = $customerMaterial->customer_sku_code;
                $customer_sku_unit = $customerMaterial->customer_sku_unit;

                // Find the code in the SapMaterial table
                $sapMaterial = SapMaterial::where('bar_code', $customer_sku_code)->first();

                if ($sapMaterial) {
                    // Add information to mappingData
                    $sap_code = $sapMaterial->sap_code;
                    $bar_code = $sapMaterial->bar_code;
                    $unit_code = $sapMaterial->unit_code;
                    $name = $sapMaterial->name;
                    $unit_id = $sapMaterial->unit_id;

                    $sapUnit = SapUnit::find($unit_id);
                    if ($sapUnit) {
                        $unit_code = $sapUnit->unit_code;
                    } else {
                        $unit_code = null; // Handle the case where the unit does not exist
                    }

                    $mappingData['customer_group_id'][$customer_group_id]['items'][] = [
                        'customer_sku_code' => $customer_sku_code,
                        'customer_sku_unit' => $customer_sku_unit,
                        'bar_code' => $bar_code,
                        'sap_code' => $sap_code,
                        'unit_id' => $unit_id,
                        'name' => $name,
                        'unit_code' => $unit_code,
                    ];
                } else {
                    $sapMaterialMappings = SapMaterialMapping::whereHas('customer_material', function ($query) use ($customer_group_id, $customer_sku_code) {
                        $query->where('customer_group_id', $customer_group_id)
                            ->where('customer_sku_code', $customer_sku_code);
                    })->get();

                    foreach ($sapMaterialMappings as $sapMaterialMapping) {
                        // Get information from the CustomerMaterial table
                        $customer_sku_code = $sapMaterialMapping->customer_material->customer_sku_code;
                        $customer_sku_unit = $sapMaterialMapping->customer_material->customer_sku_unit;

                        $sap_material_id = $sapMaterialMapping->sap_material_id;
                        $customer_material_id = $sapMaterialMapping->customer_material_id;

                        $sapMaterial = SapMaterial::find($sap_material_id);
                        $customerMaterial = CustomerMaterial::find($customer_material_id);

                        if ($sapMaterial) {
                            // Add information to mappingData
                            $sap_code = $sapMaterial->sap_code;
                            $bar_code = $sapMaterial->bar_code;
                            $unit_code = $sapMaterial->unit_code;
                            $name = $sapMaterial->name;
                            $unit_id = $sapMaterial->unit_id;

                            $sapUnit = SapUnit::find($unit_id);
                            if ($sapUnit) {
                                $unit_code = $sapUnit->unit_code;
                            } else {
                                $unit_code = null; // Handle the case where the unit does not exist
                            }

                            $mappingData[$customer_group_id]['items'][] = [
                                'customer_sku_code' => $customer_sku_code,
                                'customer_sku_unit' => $customer_sku_unit,
                                'bar_code' => $bar_code,
                                'sap_code' => $sap_code,
                                'unit_id' => $unit_id,
                                'name' => $name,
                                'unit_code' => $unit_code,
                            ];
                        }
                    }
                }
            }

            return [
                'success' => true,
                'customer_group_id' => $mappingData
            ] ?? [];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return ['success' => false, 'message' => $this->message, 'errors' => $this->errors];
        }
    }
    public function checkInventory()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'warehouse_code' => 'required|exists:warehouses,code',
                'file' => 'required|file|mimes:xlsx,xls',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $warehouse_code = request()->input('warehouse_code');
            // Trích xuất dữ liệu từ tệp tin Excel
            $file = $this->request->file('file');
            // Lưu file vào thư mục tạm
            $filePath = $file->store('temp');
            $fullPath = storage_path('app/' . $filePath);
            // Đọc file Excel
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $column_titles = ['Plant', 'Material', 'Storage', 'ATP Quantity', 'Description']; // Các tiêu đề cột cần tìm
            $column_indexes = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $row_index = $row->getRowIndex();
                // Lặp qua các ô trong hàng đầu tiên
                foreach ($row->getCellIterator() as $cell) {
                    $column_index = Coordinate::columnIndexFromString($cell->getColumn());
                    $column_title = $cell->getValue();

                    // Kiểm tra xem tiêu đề của cột có trong mảng $column_titles không
                    if (in_array($column_title, $column_titles)) {
                        $column_indexes[$column_title] = $column_index;
                    }
                }
                // Dừng sau khi tìm thấy tiêu đề của tất cả các cột cần tìm
                if (count($column_indexes) === count($column_titles)) {
                    break;
                }
            }
            $inventory_data = [];
            // Lặp qua từng dòng trong tệp tin Excel
            foreach ($worksheet->getRowIterator() as $row) {
                $row_data = [];

                // Lặp qua các ô trong hàng hiện tại
                foreach ($row->getCellIterator() as $cell) {
                    $column_index = Coordinate::columnIndexFromString($cell->getColumn());

                    // Kiểm tra xem chỉ mục cột có trong mảng $column_indexes không
                    if (in_array($column_index, $column_indexes)) {
                        $column_title = array_search($column_index, $column_indexes);
                        $column_value = $cell->getValue();
                        // Lưu trữ dữ liệu tìm thấy trong mảng $row_data
                        $row_data[$column_title] = $column_value;
                    }
                }
                // Kiểm tra xem dữ liệu liên quan đến kho có tồn tại trong hàng hiện tại không
                if (isset($row_data['Storage']) && $row_data['Storage'] === $warehouse_code) {
                    // Thêm dữ liệu vào mảng $inventoryData
                    $inventory_data[] = $row_data;
                }
            }
            // Xóa file tạm sau khi hoàn thành
            unlink($fullPath);

            return [
                'success' => true,
                'inventory' => $inventory_data,
            ];

        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
}
