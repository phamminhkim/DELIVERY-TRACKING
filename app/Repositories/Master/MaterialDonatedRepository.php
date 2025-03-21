<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialDonated;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;



class MaterialDonatedRepository extends RepositoryAbs
{
    public function getAll($is_minified, $request)
    {
        try {
            $query = MaterialDonated::query();

            if ($request->filled('search')) {
                $query->limit(50);
            }
            if ($request->filled('sap_codes')) {
                $query->whereIn('sap_code', $request->sap_codes);
            }

            if ($request->filled('ids')) {
                $query->whereIn('id', $request->ids);
            }

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            if ($request->filled('search') && $request->search != null && $request->search != 'undefined') {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('sap_code', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                });
            }
            if ($is_minified) {
                $query->select('id', 'name', 'sap_code');
            }

            $perPage = $request->filled('per_page') ? $request->per_page : 500;
            $material_donateds = $query->paginate($perPage, ['*'], 'page', $request->page);

            if ($request->filled('search') && $material_donateds->isEmpty()) {
                $material_donateds = $query->paginate($perPage, ['*'], 'page', $request->page);
            }

            $result = [
                'data' => $material_donateds->items(),
                'per_page' => $material_donateds->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $material_donateds->currentPage(),
                'last_page' => $material_donateds->lastPage(),
                'total' => $material_donateds->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createMaterialDonatedFormExcel()
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
                    'sap_code' => 0,
                    'name' => 1,
                    'is_active' => 2, // Index of the 'is_active' column in the Excel file
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    // Kiểm tra nếu có ít nhất 1 giá trị dữ liệu quan trọng (sap_code, name) thì mới xử lý
                    if (empty($row[$template_structure['sap_code']]) || empty($row[$template_structure['name']])) {
                        continue; // Bỏ qua dòng không có dữ liệu cần thiết
                    }
                    $is_active = strtolower($row[$template_structure['is_active']]);

                    // Chuyển đổi giá trị 'is_active' thành 1 nếu chuỗi là null, ngược lại thành 0
                    $is_active = ($is_active != null) ? 1 : 0;
                    $material_donated = MaterialDonated::updateOrCreate(
                        [
                            'sap_code' => $row[$template_structure['sap_code']],
                            'name' => $row[$template_structure['name']],
                        ],
                        [
                            'is_active' => $is_active,
                        ]
                    );

                    $result->push($material_donated);
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
            $query = MaterialDonated::query();

            if ($this->request->filled('search')) {
                $query->limit(50);
            }
            if ($this->request->filled('sap_codes')) {
                $query->whereIn('sap_code', $this->request->sap_codes);
            }

            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }

            if ($this->request->filled('id')) {
                $query->where('id', $this->request->id);
            }


            $material_donated = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Mã sản phẩm');
            $sheet->setCellValue('B1', 'Tên sản phẩm');
            $sheet->setCellValue('C1', 'Trạng thái');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($material_donated as $donated) {
                $sheet->setCellValue('A' . $row, $donated->sap_code);
                $sheet->setCellValue('B' . $row, $donated->name);
                if ($donated->is_active == 1) {
                    $sheet->setCellValue('C' . $row, 'x');
                }
                $row++;
            }

            // Tự căn chỉnh kích thước các cột dựa trên độ dài ký tự của dữ liệu
            $columns = ['A', 'B', 'C'];
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

            $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

            // Tạo đối tượng Writer để ghi file Excel
            $writer = new Xlsx($spreadsheet);

            // Đặt tên file và định dạng
            $filename = 'material_donateds.xlsx';

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
                    'sap_code' => 'required|unique:material_donateds,sap_code',
                    'name' => 'required',
                    'is_active' => 'in:0,1',
                ],
                [
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại',
                    'name.required' => 'Tên không được để trống',
                    'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',
                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $material_donated_data = [
                    'sap_code' => $this->data['sap_code'],
                    'name' => $this->data['name'],
                ];
                if (isset($this->data['is_active'])) {
                    $material_donated_data['is_active'] = (int) $this->data['is_active'];
                }

                $materialDonated = MaterialDonated::create($material_donated_data);



                return $materialDonated;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function update($request, $id)
    {
        try {
            $validator = Validator::make(
                $this->data,
                [
                    'sap_code' => 'integer',
                    'name' => 'required',
                    'is_active' => 'in:0,1', // Thay đổi quy tắc kiểm tra cho check_pc

                ],
                [
                    'sap_code.integer' => 'Mã SAP phải là số nguyên',
                    'name.required' => 'Tên không được để trống',
                    'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',

                ]
            );

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $materialDonated => $validator) {
                    if ($errors->has($materialDonated)) {
                        $this->errors[$materialDonated] = $errors->first($materialDonated);
                        return false;
                    }
                }
            } else {

                $materialDonated = MaterialDonated::findOrFail($id);
                $materialDonated->fill([
                    'sap_code' => $this->data['sap_code'],
                    'name' => $this->data['name'],
                ]);

                if (isset($this->data['is_active'])) {
                    $materialDonated->is_active = (int) $this->data['is_active'];
                }
                $materialDonated->save();

                return $materialDonated;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function destroy($request, $id)
    {
        try {
            $material_donated = MaterialDonated::find($id);
            $material_donated->delete();
            return $material_donated;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function destroyMultiple($ids)
    {
        try {
            $deletedItems = MaterialDonated::whereIn('id', $ids)->get(); // Lấy các bản ghi cần xóa

            if ($deletedItems->isNotEmpty()) { // Kiểm tra xem có bản ghi nào để xóa hay không
                $deletedCount = MaterialDonated::whereIn('id', $ids)->delete(); // Xóa bản ghi

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
