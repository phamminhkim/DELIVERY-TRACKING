<?php

namespace App\Repositories\Business;

use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapCompliance;
use App\Models\Master\CustomerGroup;
use App\Models\Master\MaterialCombo;
use App\Models\Master\MaterialDonated;
use App\Models\Master\MaterialCategoryType;
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

    public function checkMaterialSAP()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required',
                'items' => 'required|array',
                'items.*.customer_sku_code' => 'nullable',
                'items.*.customer_sku_unit' => 'nullable',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $customer_group_id = $this->data['customer_group_id'];
            $items = $this->data['items'];

            // Lấy thông tin nhóm khách hàng từ bảng customer_materials
            $customerMaterials = CustomerMaterial::where('customer_group_id', $customer_group_id)->get();

            if (!$customerMaterials) {
                $this->message = 'Không tìm thấy nhóm khách hàng';
                return false;
            }

            $mappingData = [];
            $existingCodes = [];

            // Tiếp tục xử lý với mảng $items chứa dữ liệu nhập vào
            foreach ($items as $item) {
                if (!empty($item['customer_sku_code'])) {
                    // Tiếp tục xử lý thông tin khi 'customer_sku_code' không trống
                    $customer_sku_code = $item['customer_sku_code'];
                    if (in_array($customer_sku_code, $existingCodes)) {
                        continue;
                    }
                    $existingCodes[] = $customer_sku_code;
                    // Kiểm tra sự tồn tại của trường 'customer_sku_unit'
                    if (isset($item['customer_sku_unit'])) {
                        $customer_sku_unit = $item['customer_sku_unit'];
                    } else {
                        $customer_sku_unit = null; // Xử lý khi trường không tồn tại
                    }
                } else {
                    continue;
                }
                // Kiểm tra xem có sự ánh xạ trực tiếp trong bảng SapMaterial hay không
                $sapMaterial = SapMaterial::where('bar_code', $customer_sku_code)->first();

                if ($sapMaterial && $sapMaterial->is_deleted != 1) {

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
                        $unit_code = null; // Xử lý khi đơn vị không tồn tại
                    }

                    $mappingData[] = [
                        'customer_sku_code' => $customer_sku_code,
                        'customer_sku_unit' => $customer_sku_unit,
                        'bar_code' => $bar_code,
                        'sap_code' => $sap_code,
                        'unit_id' => $unit_id,
                        'name' => $name,
                        'unit_code' => $unit_code,
                    ];
                } else {
                    // Kiểm tra ánh xạ trong bảng SapMaterialMapping
                    $sapMaterialMappings = SapMaterialMapping::whereHas('customer_material', function ($query) use ($customer_group_id, $customer_sku_code) {
                        $query->where('customer_group_id', $customer_group_id)
                            ->where('customer_sku_code', $customer_sku_code);
                    })->get();

                    foreach ($sapMaterialMappings as $sapMaterialMapping) {
                        $sap_material_id = $sapMaterialMapping->sap_material_id;

                        $sapMaterial = SapMaterial::find($sap_material_id);

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
                                $unit_code = null; // Xử lý khi đơn vị không tồn tại
                            }

                            $mappingData[] = [
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
                'items' => $mappingData
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function checkCompliance()
    {
        try {
            $validator = Validator::make($this->data, [
                'items' => 'required|array',
                'items.*.sap_code' => 'required',
                'items.*.unit_code' => 'required',
                'items.*.quantity2_po' => 'required',
            ]);
            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }
            $check_compliance = [];
            foreach ($this->data['items'] as $item) {
                $sap_code = $item['sap_code'];
                $unit_code = $item['unit_code'];
                $quantity2_po = $item['quantity2_po'];
                // Kiểm tra xem có đơn vị tính trong bảng SapCompliance hay không
                $sapCompliance = SapCompliance::where('sap_code', $sap_code)
                    ->whereHas('unit', function ($query) use ($unit_code) {
                        $query->where('unit_code', $unit_code);
                    })
                    ->first();

                if ($sapCompliance) {
                    $unit_code = $sapCompliance->unit->unit_code;
                    $compliance = $sapCompliance->compliance;

                    $itemData = [
                        'sap_code' => $sap_code,
                        'unit_code' => $unit_code,
                        'quantity2_po' => $quantity2_po,
                    ];
                    // Kiểm tra dữ liệu quy cách
                    if ($compliance !== null) {
                        $itemData['compliance'] = $compliance;
                        // Kiểm tra chia hết cho compliance
                        if ($compliance !== 0 && $quantity2_po % $compliance !== 0) {
                            $itemData['is_compliant'] = false;
                        } else {
                            $itemData['is_compliant'] = true;
                        }
                    } else {
                        $itemData['compliance'] = null;
                        $itemData['is_compliant'] = true;
                    }
                    $check_compliance[] = $itemData;
                }
            }
            $result = [
                'success' => true,
                'items' => $check_compliance
            ];
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function checkPromotions()
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'customer_group_id' => 'required',
                'items' => 'required|array',
                'items.*.sap_code' => '',
                'items.*.bar_code' => '',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            $customer_group_id = $this->request->input('customer_group_id');
            $items = $this->request->input('items');

            $customerMaterials = CustomerMaterial::where('customer_group_id', $customer_group_id)->get();

            if (!$customerMaterials) {
                $this->message = 'Không tìm thấy nhóm khách hàng';
                return false;
            }
            $mappingData = [];
            foreach ($items as $item) {
                $sap_code = isset($item['sap_code']) ? $item['sap_code'] : null;
                $bar_code = isset($item['bar_code']) ? $item['bar_code'] : null;
                $name = isset($item['name']) ? $item['name'] : null;
                $promotion_category = isset($item['promotion_category']) ? $item['promotion_category'] : null;

                $materialCombo = null;
                $extra_offer = ''; //
                $promotion_category = ''; //

                // Kiểm tra xem sap_code hoặc bar_code có được cung cấp hay không
                if (!empty($sap_code) || !empty($bar_code)) {
                    $materialCombo = MaterialCombo::where('customer_group_id', $customer_group_id)
                        ->when(!empty($sap_code), function ($query) use ($sap_code) {
                            return $query->where('sap_code', $sap_code);
                        })
                        ->when(!empty($bar_code), function ($query) use ($bar_code) {
                            return $query->where('bar_code', $bar_code);
                        })
                        ->first();
                }
                if ($materialCombo) {
                    // $combo_category_type = MaterialCategoryType::where('name', 'Combo')
                    //     ->where('is_deleted', false)
                    //     ->first();

                    // $promotion_category = $combo_category_type ? 'Combo' : null;
                    $promotion_category = 'X';
                    $name = $materialCombo->name;
                } else {
                    $sapMaterial = SapMaterial::where('sap_code', $sap_code)->first();

                    if ($sapMaterial) {
                        $name = $sapMaterial->name;

                        $materialDonated = MaterialDonated::where('sap_code', $sap_code)->first();

                        if ($materialDonated) {
                            // $donated_category_type = MaterialCategoryType::where('name', 'ExtraOffer')
                            //     ->where('is_deleted', false)
                            //     ->first();

                            // $promotion_category = $donated_category_type ? 'ExtraOffer' : null;
                            $extra_offer  = 'X';
                        }
                    }
                }
                // if ($promotion_category !== null) {
                // $item['promotion_category'] = $promotion_category;
                $item['name'] = $name;
                if ($promotion_category == 'X' || $extra_offer == 'X') {
                    $mappingData[] = [
                        'sap_code' => $sap_code,
                        'bar_code' => $bar_code,
                        'name' => $name,
                        'promotion_category' => $promotion_category,
                        'extra_offer' =>  $extra_offer,
                    ];
                }

                // }
            }
            return [
                'success' => true,
                'items' => $mappingData
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
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

                        // Đặt tên biến mới cho "ATP Quantity"
                        if ($column_title === 'ATP Quantity') {
                            $column_title = 'ATP_Quantity';
                        }

                        // Lưu trữ dữ liệu tìm thấy trong mảng $row_data
                        $row_data[$column_title] = $column_value;
                    }
                }
                // Kiểm tra xem dữ liệu liên quan đến kho có tồn tại trong hàng hiện tại không
                if (isset($row_data['Storage']) && $row_data['Storage'] === $warehouse_code) {
                    // Thêm dữ liệu vào mảng $inventory_data
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
    public function checkPrice()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'file' => 'required|file|mimes:xlsx,xls',
            ]);

            if ($validator->fails()) {
                $this->message = $validator->errors()->first();
                return false;
            }

            // Trích xuất dữ liệu từ tệp tin Excel
            $file = $this->request->file('file');
            // Lưu file vào thư mục tạm
            $filePath = $file->store('temp');
            $fullPath = storage_path('app/' . $filePath);
            // Đọc file Excel
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $column_titles = ['Ma SP', 'Barcode (Mã BH)', 'Tên Sản Phẩm', 'ĐVT', 'Đơn giá']; // Các tiêu đề cột cần tìm
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
            $check_price = [];
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
                        // Đặt tên biến mới cho các trường dữ liệu
                        $new_column_title = '';
                        if ($column_title === 'Ma SP') {
                            $new_column_title = 'sap_code';
                        } elseif ($column_title === 'Barcode (Mã BH)') {
                            $new_column_title = 'bar_code';
                        } elseif ($column_title === 'Tên Sản Phẩm') {
                            $new_column_title = 'sap_name';
                        } elseif ($column_title === 'ĐVT') {
                            $new_column_title = 'unit';
                        } elseif ($column_title === 'Đơn giá') {
                            $new_column_title = 'price';
                        } else {
                            // Không thực hiện thay đổi tên biến cho các trường khác
                            $new_column_title = $column_title;
                        }
                        // Lưu trữ dữ liệu tìm thấy trong mảng $row_data
                        $row_data[$new_column_title] = $column_value;
                    }
                }
                // Kiểm tra xem dữ liệu liên quan đến mã vạch có tồn tại trong hàng hiện tại không
                if (isset($row_data['bar_code']) && $row_data['bar_code'] !== '') {
                    $check_price[] = $row_data;
                } elseif (isset($row_data['sap_code']) && $row_data['sap_code'] !== '') {
                    $check_price[] = $row_data;
                }
            }
            // Xóa file tạm sau khi hoàn thành
            unlink($fullPath);

            return [
                'success' => true,
                'price' => $check_price,
            ];
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
}
