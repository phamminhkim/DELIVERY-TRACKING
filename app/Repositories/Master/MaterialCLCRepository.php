<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialCLC;
use App\Models\Master\CustomerGroup;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\Excel\ExcelExtractor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MaterialCLCRepository extends RepositoryAbs
{
    public function getAll($is_minified)
    {
        try {
            $query = MaterialCLC::query();
            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('sap_codes')) {
                $query = $query->whereIn('sap_code', $this->request->sap_codes);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($is_minified) {
                $query->select('id', 'sap_code', 'name', 'bar_code');
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                if (!is_array($customer_group_ids)) {
                    $customer_group_ids = explode(',', $customer_group_ids);
                }
                $query->whereIn('customer_group_id', $customer_group_ids);
            }
            $query->with([
                'customer_group' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ]);

            $perPage = $this->request->filled('per_page') ? $this->request->per_page : 500;
            $material_clc = $query->paginate($perPage, ['*'], 'page', $this->request->page);

            if ($this->request->filled('search') && $material_clc->isEmpty()) {
                $material_clc = $query->paginate($perPage, ['*'], 'page', $this->request->page);
            }

            $result = [
                'data' => $material_clc->items(),
                'per_page' => $material_clc->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $material_clc->currentPage(),
                'last_page' => $material_clc->lastPage(),
                'total' => $material_clc->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createMaterialCLCFormExcel()
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
                return false;
            } else {
                $file = $this->request->file('file');
                $excel_extractor = new ExcelExtractor();
                $data = $excel_extractor->extractData($file);
                $template_structure = [
                    'customer_group_name' => 0,
                    'sap_code' => 1,
                    'name' => 2,
                    'bar_code' => 3,
                    'is_active' => 4, // Thêm trường is_active vào cấu trúc mẫu
                ];
                $result = [];

                foreach ($data as $row) {
                    // Kiểm tra nếu có ít nhất 1 giá trị dữ liệu quan trọng (sap_code, name, customer_group_name) thì mới xử lý
                    if (empty($row[$template_structure['sap_code']]) || empty($row[$template_structure['name']]) || empty($row[$template_structure['customer_group_name']])) {
                        continue; // Bỏ qua dòng không có dữ liệu cần thiết
                    }
                    $sap_code = $row[$template_structure['sap_code']];
                    $name = $row[$template_structure['name']];
                    $bar_code = $row[$template_structure['bar_code']];
                    $is_active = $row[$template_structure['is_active']];
                    $customer_group_names = explode(',', $row[$template_structure['customer_group_name']]);

                    foreach ($customer_group_names as $customer_group_name) {
                        $customer_group = CustomerGroup::where('name', $customer_group_name)->first();

                        if (!$customer_group) {
                            $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customer_group_name;
                            continue;
                        }

                        $existing_clc = MaterialCLC::where('sap_code', $sap_code)
                            ->where('customer_group_id', $customer_group->id)
                            ->first();

                        if ($existing_clc) {
                            // Bộ mã đã tồn tại, cập nhật thông tin
                            $existing_clc->name = $name;
                            $existing_clc->bar_code = $bar_code;
                            $existing_clc->is_active = ($is_active !== null) ? 1 : 0; // Cập nhật trường is_active
                            $existing_clc->save();

                            $result[] = $existing_clc;
                        } else {
                            // Tạo mới bộ mã
                            $new_material_clc = MaterialCLC::create([
                                'customer_group_id' => $customer_group->id,
                                'sap_code' => $sap_code,
                                'name' => $name,
                                'bar_code' => $bar_code,
                                'is_active' => ($is_active !== null) ? 1 : 0, // Cập nhật trường is_active
                            ]);

                            $result[] = $new_material_clc;
                        }
                    }
                }
                return [
                    "created_list" => $result,
                    "errors" => $this->errors
                ];
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function exportToExcel()
    {
        try {
            $query = MaterialCLC::query();
            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('sap_codes')) {
                $query = $query->whereIn('sap_code', $this->request->sap_codes);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            $query->with([
                'customer_group' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ]);

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                if (!is_array($customer_group_ids)) {
                    $customer_group_ids = explode(',', $customer_group_ids);
                }
                $query->whereHas('customer_group', function ($query) use ($customer_group_ids) {
                    $query->whereIn('id', $customer_group_ids);
                });
            }

            $material_clc = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Nhóm khách hàng');
            $sheet->setCellValue('B1', 'Mã sản phẩm');
            $sheet->setCellValue('C1', 'Tên sản phẩm');
            $sheet->setCellValue('D1', 'Mã Barcode');
            $sheet->setCellValue('E1', 'Trạng thái');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($material_clc as $clc) {
                $sheet->setCellValue('A' . $row, $clc->customer_group->name);
                $sheet->setCellValue('B' . $row, $clc->sap_code);
                $sheet->setCellValue('C' . $row, $clc->name);
                $sheet->setCellValue('D' . $row, $clc->bar_code);
                if ($clc->is_active == 1) {
                    $sheet->setCellValue('E' . $row, 'x');
                }
                $row++;
            }

            // Tự căn chỉnh kích thước các cột dựa trên độ dài ký tự của dữ liệu
            $columns = ['A', 'B', 'C', 'D', 'E'];
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

            $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

            // Tạo đối tượng Writer để ghi file Excel
            $writer = new Xlsx($spreadsheet);

            // Đặt tên file và định dạng
            $filename = 'material_clcs.xlsx';

            // Đặt header cho response
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            //Ghi file Excel vào output
            $writer->save('php://output');
        } catch (\Exception  $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function store()
    {
        try {
            $validator = Validator::make(
                $this->data,
                [
                    'customer_group_id' => 'required',
                    'sap_code' => 'required|unique:material_clcs,sap_code,NULL,id,customer_group_id,' . $this->data['customer_group_id'],
                    // 'bar_code' => 'string',
                    'name' => 'required',
                    'is_active' => 'in:0,1',

                ],
                [
                    'customer_group_id.required' => 'Mã SAP không được để trống',
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại trong nhóm khách hàng này',
                    // 'bar_code.string' => 'Tên không được để trống',
                    'name.required' => 'Tên không được để trống',
                    'is_active.in' => 'Trạng thái chỉ được chứa giá trị 0 hoặc 1.',

                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $customer_group = CustomerGroup::find($this->data['customer_group_id']);
                if (!$customer_group) {
                    $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $this->data['customer_group_id'];
                    return false;
                }

                $material_clc = MaterialCLC::create($this->data);
                return $material_clc;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function update($id)
    {
        try {
            $material_clc = MaterialCLC::where('id', $id)->firstOrFail();

            $validator = Validator::make($this->data, [
                'customer_group_id' => 'integer',
                'sap_code' => 'string',
                'bar_code' => 'string',
                'name' => 'string',
                'is_active' => 'in:0,1',

            ], [
                'customer_group_id.integer' => 'Nhóm khách hàng phải là số nguyên.',
                'sap_code.string' => 'Mã sản phẩm phải là chuỗi.',
                'bar_code.string' => 'Mã sản phẩm phải là chuỗi.',
                'name.string' => 'Mã sản phẩm phải là chuỗi.',
                'is_active.in' => 'Trạng thái chỉ được chứa giá trị 0 hoặc 1.',

            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }

            $customer_group = CustomerGroup::find($this->data['customer_group_id']);
            if (!$customer_group && $this->data['customer_group_id']) {
                $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $this->data['customer_group_id'];
                return false;
            }
            $material_clc = MaterialCLC::findOrFail($id);

            // Tiếp tục với việc cập nhật dữ liệu
            $material_clc->update($this->data);
            return $material_clc;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    public function destroy($request, $id)
    {
        try {
            $material_clc = MaterialClc::find($id);
            $material_clc->delete();
            return $material_clc;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function destroyMultiple($ids)
    {
        try {
            $deletedItems = MaterialCLC::whereIn('id', $ids)->get(); // Lấy các bản ghi cần xóa

            if ($deletedItems->isNotEmpty()) { // Kiểm tra xem có bản ghi nào để xóa hay không
                $deletedCount = MaterialCLC::whereIn('id', $ids)->delete(); // Xóa bản ghi

                return $deletedCount;
            } else {
                return 0; // Trả về 0 nếu không có bản ghi nào được xóa
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
