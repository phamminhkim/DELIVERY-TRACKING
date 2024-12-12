<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialParker;
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

class MaterialParkerRepository extends RepositoryAbs
{
    public function getAll($is_minified)
    {
        try {
            $query = MaterialParker::query();
            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('sap_codes')) {
                $query = $query->whereIn('sap_code', $this->request->sap_codes);
            }
            if ($this->request->filled('bar_codes')) {
                $query = $query->whereIn('bar_code', $this->request->bar_codes);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($is_minified) {
                $query->select('id', 'sap_code', 'name', 'bar_code');
            }            
            

            $perPage = $this->request->filled('per_page') ? $this->request->per_page : 500;
            $material_parker = $query->paginate($perPage, ['*'], 'page', $this->request->page);

            if ($this->request->filled('search') && $material_parker->isEmpty()) {
                $material_parker = $query->paginate($perPage, ['*'], 'page', $this->request->page);
            }

            $result = [
                'data' => $material_parker->items(),
                'per_page' => $material_parker->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $material_parker->currentPage(),
                'last_page' => $material_parker->lastPage(),
                'total' => $material_parker->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createMaterialParkerFormExcel()
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
                    'bar_code' => 0,
                    'sap_code' => 1,                    
                    'name' => 2,
                    'is_active' => 3, // Index of the 'is_active' column in the Excel file
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    $is_active = strtolower($row[$template_structure['is_active']]);

                    // Chuyển đổi giá trị 'is_active' thành 1 nếu chuỗi là null, ngược lại thành 0
                    $is_active = ($is_active != null) ? 1 : 0;
                    $material_parker = MaterialParker::updateOrCreate(
                        [
                            'bar_code' => $row[$template_structure['bar_code']],
                            'sap_code' => $row[$template_structure['sap_code']],                            
                            'name' => $row[$template_structure['name']],
                        ],
                        [
                            'is_active' => $is_active,
                        ]
                    );

                    $result->push($material_parker);
                }

                return [
                    'created_list' => $result,
                    'errors' => $this->errors,
                ];
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
            $query = MaterialParker::query();
            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('sap_codes')) {
                $query = $query->whereIn('sap_code', $this->request->sap_codes);
            }
            if ($this->request->filled('bar_codes')) {
                $query = $query->whereIn('bar_code', $this->request->bar_codes);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            

            $material_parker = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Mã Barcode');
            $sheet->setCellValue('B1', 'Mã sản phẩm');
            $sheet->setCellValue('C1', 'Tên sản phẩm');
            $sheet->setCellValue('D1', 'Trạng thái');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($material_parker as $parker) {
                $sheet->setCellValue('A' . $row, $parker->bar_code);
                $sheet->setCellValue('B' . $row, $parker->sap_code);
                $sheet->setCellValue('C' . $row, $parker->name);
                if ($parker->is_active == 1) {
                    $sheet->setCellValue('D' . $row, 'x');
                }
                $row++;
            }

            // Tự căn chỉnh kích thước các cột dựa trên độ dài ký tự của dữ liệu
            $columns = ['A', 'B', 'C', 'D'];
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

            $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

            // Tạo đối tượng Writer để ghi file Excel
            $writer = new Xlsx($spreadsheet);

            // Đặt tên file và định dạng
            $filename = 'material_parkers.xlsx';

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
                    'sap_code' => 'required|unique:material_parkers,sap_code',
                    'name' => 'required',
                    'bar_code' => 'required',
                    'is_active' => 'in:0,1',
                ],
                [
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại',
                    'name.required' => 'Tên không được để trống',
                    'bar_code.required' => 'Barcode không được để trống',
                    'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',
                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $material_parker_data = [
                    'sap_code' => $this->data['sap_code'],
                    'name' => $this->data['name'],
                ];
                if (isset($this->data['is_active'])) {
                    $material_parker_data['is_active'] = (int) $this->data['is_active'];
                }

                $materialParker = MaterialParker::create($material_parker_data);



                return $materialParker;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function update($id)
    {
        try {
            $material_parker = MaterialParker::where('id', $id)->firstOrFail();

            $validator = Validator::make($this->data, [
                'sap_code' => 'string',
                'bar_code' => 'string',
                'name' => 'string',
                'is_active' => 'in:0,1',

            ], [
                'sap_code.string' => 'Mã sản phẩm phải là chuỗi.',
                'bar_code.string' => 'Mã sản phẩm phải là chuỗi.',
                'name.string' => 'Mã sản phẩm phải là chuỗi.',
                'is_active.in' => 'Trạng thái chỉ được chứa giá trị 0 hoặc 1.',

            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }
            $material_parker = MaterialParker::findOrFail($id);

            // Tiếp tục với việc cập nhật dữ liệu
            $material_parker->update($this->data);
            return $material_parker;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    public function destroy($request, $id)
    {
        try {
            $material_parker = MaterialParker::find($id);
            $material_parker->delete();
            return $material_parker;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function destroyMultiple($ids)
    {
        try {
            $deletedItems = MaterialParker::whereIn('id', $ids)->get(); // Lấy các bản ghi cần xóa

            if ($deletedItems->isNotEmpty()) { // Kiểm tra xem có bản ghi nào để xóa hay không
                $deletedCount = MaterialParker::whereIn('id', $ids)->delete(); // Xóa bản ghi

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
