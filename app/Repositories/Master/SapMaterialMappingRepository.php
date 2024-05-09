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
use Monolog\Formatter\JsonFormatter;
use Psy\Util\Json;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
                    'customer_number' => 3,
                    'conversion_rate_sap' => 7,
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
                        $this->errors[] = 'Không tìm thấy mã hàng sap (' . $material['sap_material_code'] . ') với đơn vị tính (' . $material['sap_material_unit'] .  ')';
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
                    $totalPercentage = SapMaterialMapping::query()
                        ->where('customer_material_id', $customer_material->id)
                        ->sum('percentage');

                    $newTotalPercentage = $totalPercentage + $material['percentage'];

                    if ($newTotalPercentage > 100) {
                        $this->errors[] = 'Mã hàng khách hàng ' . $material['customer_material_sku_code'] . ' đã được map với mã hàng SAP với tổng tỷ lệ đã đủ/vượt quá 100%';
                        continue;
                    }

                    $sap_material_mapping = SapMaterialMapping::create([
                        'customer_material_id' => $customer_material->id,
                        'sap_material_id' => $sap_material->id,
                        'conversion_rate_sap' => $material['conversion_rate_sap'],
                        'customer_number' => $material['customer_number'],
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
            return false;
        }
    }

    public function exportToExcel()
    {
        try {
            $query = SapMaterialMapping::query();

            // Thêm các điều kiện tìm kiếm vào câu truy vấn
            if (request()->filled('search')) {
                $query->search(request()->search);
                $query->limit(200);
            }

            if (request()->filled('customer_material_ids')) {
                $customer_material_ids = request()->customer_material_ids;
                $query->whereIn('customer_material_id', $customer_material_ids);
            }

            if (request()->filled('sap_material_ids')) {
                $sap_material_ids = request()->sap_material_ids;
                $query->whereIn('sap_material_id', $sap_material_ids);
            }

            if (request()->filled('customer_group_ids')) {
                $customer_group_ids = (array) request()->customer_group_ids;
                $query->whereHas('customer_material', function ($subQuery) use ($customer_group_ids) {
                    $subQuery->whereIn('customer_group_id', $customer_group_ids);
                });
            }

            $query->with([
                'customer_material' => function ($query) {
                    $query->select(['id', 'customer_group_id', 'customer_sku_code', 'customer_sku_name', 'customer_sku_unit']);
                    $query->with('customer_group:id,name');
                },
                'sap_material' => function ($query) {
                    $query->select(['id', 'sap_code', 'unit_id', 'name']);
                    $query->with('unit:id,unit_code');
                },
            ]);

            $sapMaterialMappings = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Nhóm khách hàng');
            $sheet->setCellValue('B1', 'Mã SKU KH');
            $sheet->setCellValue('C1', 'Tên SKU KH');
            $sheet->setCellValue('D1', 'Số lượng - SKU KH');
            $sheet->setCellValue('E1', 'Đơn vị SKU KH');
            $sheet->setCellValue('F1', 'Mã SAP');
            $sheet->setCellValue('G1', 'Tên SAP');
            $sheet->setCellValue('H1', 'Đơn vị tính SAP');
            $sheet->setCellValue('I1', 'Số lượng SAP');
            $sheet->setCellValue('J1', 'Tỉ lệ');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($sapMaterialMappings as $mapping) {
                $sheet->setCellValue('A' . $row, $mapping->customer_material->customer_group->name);
                $sheet->setCellValue('B' . $row, $mapping->customer_material->customer_sku_code);
                $sheet->setCellValue('C' . $row, $mapping->customer_material->customer_sku_name);
                $sheet->setCellValue('D' . $row, $mapping->customer_material->customer_number); // Thay đổi thành cột số lượng SKU KH
                $sheet->setCellValue('E' . $row, $mapping->customer_material->customer_sku_unit);
                $sheet->setCellValue('F' . $row, $mapping->sap_material->sap_code);
                $sheet->setCellValue('G' . $row, $mapping->sap_material->name);
                $sheet->setCellValue('H' . $row, $mapping->sap_material->unit->unit_code);
                $sheet->setCellValue('I' . $row, $mapping->sap_material->conversion_rate_sap); // Thêm cột số lượng SAP
                $sheet->setCellValue('J' . $row, $mapping->percentage);
                $row++;
            }

            // Tự căn chỉnh kích thước các cột dựa trên độ dài ký tự của dữ liệu
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',];
            foreach ($columns as $column) {
                $columnDimension = $sheet->getColumnDimension($column);
                $columnWidth = $columnDimension->getWidth();
                $highestRow = $sheet->getHighestRow();
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell($column . $row)->getValue();
                    $cellLength = mb_strlen($cellValue);
                    $columnWidth = max($columnWidth, $cellLength);
                }
                $columnDimension->setWidth($columnWidth + 1); // Thêm một đơn vị cho khoảng cách giữa cột và nội dung
            }

            // Đặt style cho header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B0C4DE'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

            // Tạo đối tượng Writer để ghi file Excel
            $writer = new Xlsx($spreadsheet);

            // Đặt tên file và định dạng
            $filename = 'sap_material_mappings.xlsx';

            // Đặt header cho response
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Ghi file Excel vào output
            $writer->save('php://output');
        } catch (\Exception  $exception) {
            // Xử lý ngoại lệ nếu có
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return back()->withErrors(['error' => 'An error occurred while exporting to Excel']);
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
            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = (array) $this->request->customer_group_ids;
                $query->whereHas('customer_material', function ($subQuery) use ($customer_group_ids) {
                    $subQuery->whereIn('customer_group_id', $customer_group_ids);
                });
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

            $sapMaterialMappings = $query->paginate(PHP_INT_MAX);

            $result = [
                'data' => $sapMaterialMappings->items(),
                'per_page' => $sapMaterialMappings->perPage(),
            ];

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

    public function createNewSapMaterialMappings($customerMaterialData)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($customerMaterialData, [
                // 'customer_material_id' => 'nullable|integer|exists:customer_materials,id',
                'sap_material_id' => 'required|integer|exists:sap_materials,id',
                'percentage' => 'required|integer',
                'customer_number' => 'required|integer',
                'conversion_rate_sap' => 'required|integer',
                'customer_group_id' => 'required',
                'customer_sku_code' => 'required', //|unique:customer_materials,customer_sku_code
                'customer_sku_name' => 'required',
                'customer_sku_unit' => 'required',
            ], [
                // 'customer_material_id.integer' => 'Mã SKU khách hàng phải là số nguyên.',
                'sap_material_id.required' => 'Yêu cầu nhập mã đối chiếu.',
                'sap_material_id.integer' => 'Mã đối chiếu phải là số nguyên.',
                'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
                'percentage.required' => 'Yêu cầu nhập tỷ lệ sản phẩm.',
                'percentage.integer' => 'Tỷ lệ sản phẩm phải là số nguyên.',
                'customer_number.required' => 'Yêu cầu nhập số lượng khách hàng.',
                'customer_number.integer' => 'Số lượng khách hàng phải là số nguyên.',
                'conversion_rate_sap.required' => 'Yêu cầu nhập tỷ lệ chuyển đổi SAP.',
                'conversion_rate_sap.integer' => 'Tỷ lệ chuyển đổi SAP phải là số nguyên.',
                'customer_sku_code.required' => 'Yêu cầu nhập mã SKU khách hàng.',
                'customer_sku_name.required' => 'Yêu cầu nhập tên SKU khách hàng.',
                'customer_sku_unit.required' => 'Yêu cầu nhập mã unit khách hàng.',
                // 'customer_sku_code.unique' => 'Mã SKU khách hàng đã tồn tại.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }

            $sap_material = SapMaterial::find($customerMaterialData['sap_material_id']);
            if (!$sap_material) {
                $this->errors[] = 'Không tìm thấy mã đối chiếu sản phẩm ' . $customerMaterialData['sap_material_id'];
                return false;
            }

            $customer_group = CustomerGroup::find($customerMaterialData['customer_group_id']);
            if (!$customer_group) {
                $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customerMaterialData['customer_group_id'];
                return false;
            }

            $customer_sku_code = $customerMaterialData['customer_sku_code'] ?? '';

            $customer_material_existed = CustomerMaterial::where('customer_group_id', $customer_group->id)
                ->where('customer_sku_code', $customer_sku_code)
                ->where('customer_sku_unit', $customerMaterialData['customer_sku_unit'])
                ->first();

            $total_existing_percent = $customer_material_existed
                ? $customer_material_existed->mappings()->sum('percentage')
                : 0;

            if ($customer_material_existed) {
                $new_total_percent = $total_existing_percent + $customerMaterialData['percentage'];

                if ($new_total_percent > 100) {
                    $this->errors = [
                        'percentage' => 'Tổng tỷ lệ sản phẩm vượt quá 100.',
                        'customer_sku_code' => 'Mã SKU khách hàng đã tồn tại trong ' . $customer_group->name,
                        'customer_sku_unit' => 'Mã Unit khách hàng đã tồn tại trong ' . $customer_group->name,
                    ];
                    return false; // Dừng việc thêm mục mới và hiển thị thông báo lỗi
                }
                // // Tính giá trị conversion_rate_sap dựa trên tỷ lệ percentage nhập vào
                // $conversion_rate_sap = $customerMaterialData['conversion_rate_sap'] * $customerMaterialData['percentage'] / 100;

                // // Kiểm tra nếu conversion_rate_sap bằng 0 hoặc là số thập phân lẻ
                // if ($conversion_rate_sap == 0 || fmod($conversion_rate_sap, 1) != 0) {
                //     $this->errors = [
                //         'conversion_rate_sap' => 'Giá trị conversion_rate_sap không hợp lệ.',
                //     ];
                //     return false; // Dừng việc thêm mục mới và hiển thị thông báo lỗi
                // }

                $sap_material_mapping = $customer_material_existed->mappings()->create([
                    'sap_material_id' => $customerMaterialData['sap_material_id'],
                    'percentage' => $customerMaterialData['percentage'],
                    'customer_number' => $customerMaterialData['customer_number'],
                    'conversion_rate_sap' => $customerMaterialData['conversion_rate_sap'],
                ]);

                DB::commit();
                return $sap_material_mapping;
            }

            if ($customer_material_existed) {
                $customer_material = $customer_material_existed;
                $customer_material->fill([
                    'customer_sku_code' => $customer_sku_code,
                    'customer_sku_name' => $customerMaterialData['customer_sku_name'] ?? '',
                    'customer_sku_unit' => $customerMaterialData['customer_sku_unit'] ?? '',

                ]);
                $customer_material->save();
            } else {
                // Create a new customer material
                $customer_material = CustomerMaterial::create([
                    'customer_group_id' => $customer_group->id,
                    'customer_sku_code' => $customer_sku_code,
                    'customer_sku_name' => $customerMaterialData['customer_sku_name'] ?? '',
                    'customer_sku_unit' => $customerMaterialData['customer_sku_unit'] ?? '',
                ]);
            }

            $customer_material_id = $customer_material->id;

            $existing_mapping = SapMaterialMapping::where('customer_material_id', $customer_material_id)
                ->where('sap_material_id', $customerMaterialData['sap_material_id'])
                ->exists();

            if ($existing_mapping) {
                // $this->errors[] = 'Mapping dữ liệu đã tồn tại.';
                $error['sap_material_id'] = array('Mapping dữ liệu đã tồn tại.');
                return false;
            }

            $sap_material_mapping = SapMaterialMapping::create([
                'customer_material_id' => $customer_material_id,
                'sap_material_id' => $customerMaterialData['sap_material_id'],
                'percentage' => $customerMaterialData['percentage'],
                'customer_number' => $customerMaterialData['customer_number'],
                'conversion_rate_sap' => $customerMaterialData['conversion_rate_sap'],
            ]);

            DB::commit();
            return $sap_material_mapping;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            DB::rollBack();
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
            $editableFields = [
                'customer_sku_name',
                'percentage',
                'customer_number',
                'conversion_rate_sap',
                'sap_material_id',
                'customer_group_id',
                'customer_sku_code',
                'customer_sku_unit',
            ];
            $editableData = array_intersect_key($customerMaterialData, array_flip($editableFields));
            $validator = Validator::make($editableData, [
                'customer_sku_name' => 'required',
                'percentage' => 'required|integer',
                'customer_number' => 'required|integer',
                'conversion_rate_sap' => 'required|integer',
                'sap_material_id' => 'not_in:0',
                'customer_group_id' => 'not_in:0',
                'customer_sku_code' => 'not_in:0',
                'customer_sku_unit' => 'not_in:0',
            ], [
                'customer_sku_name.required' => 'Tên SKU khách hàng là bắt buộc.',
                'percentage.required' => 'Tỷ lệ sản phẩm là bắt buộc.',
                'percentage.integer' => 'Tỷ lệ sản phẩm phải là số nguyên.',
                'customer_number.required' => 'Số lượng khách hàng sản phẩm là bắt buộc.',
                'customer_number.integer' => 'Số lượng khách hàng phải là số nguyên.',
                'conversion_rate_sap.required' => 'Tỷ lệ chuyển đổi là bắt buộc.',
                'conversion_rate_sap.integer' => 'Tỷ lệ chuyển đổi là số nguyên.',
                'sap_material_id.not_in' => 'Không được chỉnh sửa trường sap_material_id.',
                'customer_group_id.not_in' => 'Không được chỉnh sửa trường customer_group_id.',
                'customer_sku_code.not_in' => 'Không được chỉnh sửa trường customer_sku_code.',
                'customer_sku_unit.not_in' => 'Không được chỉnh sửa trường customer_sku_unit.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
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
            // tính tỷ lệ màu
            $customer_material_existed = CustomerMaterial::where('customer_group_id', $customer_material->customer_group_id)
                ->where('customer_sku_code', $customer_material->customer_sku_code)
                ->where('customer_sku_unit', $customer_material->customer_sku_unit)
                ->first();

            $total_existing_percent = $customer_material_existed
                ? $customer_material_existed->mappings()->sum('percentage')
                : 0;

            $new_total_percent = $total_existing_percent - $sap_material_mapping->percentage + $customerMaterialData['percentage'];

            // if ($new_total_percent > 100) {
            //     $this->errors = [
            //         'percentage' => 'Tổng tỷ lệ sản phẩm vượt quá 100.',
            //         'customer_sku_code' => 'Mã SKU khách hàng đã tồn tại trong ' . $customer_group->name,
            //         'customer_sku_unit' => 'Mã Unit khách hàng đã tồn tại trong ' . $customer_group->name,
            //     ];
            //     return false; // Stop updating the item and display the error message
            // }
            // Bắt đầu giao dịch
            DB::beginTransaction();

            try {
                $initialValues = [
                    'sap_material_id' => $sap_material_mapping->sap_material_id,
                    'customer_group_id' => $sap_material_mapping->customer_group_id,
                    'customer_sku_code' => $customer_material->customer_sku_code,
                    'customer_sku_unit' => $customer_material->customer_sku_unit,
                ];

                $hasChanges = false; // Biến kiểm tra trạng thái thay đổi

                foreach ($initialValues as $field => $value) {
                    if ($customerMaterialData[$field] !== $value) {
                        $hasChanges = true;
                        break;
                    }
                }

                if (!$hasChanges) {
                    // Không có thay đổi, bỏ qua
                    DB::commit();
                    return $sap_material_mapping;
                }

                $customer_material->customer_sku_name = $customerMaterialData['customer_sku_name'];
                $customer_material->save();

                $sap_material_mapping->percentage = $customerMaterialData['percentage'];
                $sap_material_mapping->customer_number = $customerMaterialData['customer_number'];
                $sap_material_mapping->conversion_rate_sap = $customerMaterialData['conversion_rate_sap'];
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
            DB::rollBack();
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
