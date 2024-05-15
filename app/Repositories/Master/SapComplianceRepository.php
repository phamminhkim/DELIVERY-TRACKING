<?php

namespace App\Repositories\Master;

use App\Models\Master\SapCompliance;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class SapComplianceRepository extends RepositoryAbs
{
    public function getAvailableSapCompliances($is_minified, $request)
    {
        try {
            $query = SapCompliance::query();

            // if ($request->filled('search')) {
            //     $query->with(['unit']);
            //     $query->limit(50);
            // }

            if ($request->filled('bar_codes')) {
                $query->whereIn('bar_code', $request->bar_codes);
            }
            if ($request->filled( 'bar_codes', 'sap_codes','names')) {
                $query->whereIn('bar_code', $request->bar_codes)
                    ->orWhereIn('sap_code', $request->sap_codes)
                    ->orWhereIn('name', $request->names);
            }


            if ($request->filled('unit_ids')) {
                $query->whereIn('unit_id', $request->unit_ids);
            }

            if ($request->filled('ids')) {
                $query->whereIn('id', $request->ids);
            }

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            // if ($request->filled('search') && $request->search != null && $request->search != 'undefined') {
            //     $search = $request->input('search');
            //     $query->where(function ($q) use ($search) {
            //         $q->where('sap_code', 'like', "%$search%")
            //             ->orWhere('name', 'like', "%$search%");
            //     });
            // }
            if ($is_minified) {
                $query->select('id', 'name', 'sap_code', 'unit_id', 'bar_code');
            }

            $query->with(['unit' => function ($query) {
                $query->select(['id', 'unit_code']);
            }]);

            $perPage = $request->filled('per_page') ? $request->per_page : 500;
            $sap_compliances = $query->paginate($perPage, ['*'], 'page', $request->page);

            // if ($request->filled('search') && $sap_compliances->isEmpty()) {
            //     $sap_compliances = $query->paginate($perPage, ['*'], 'page', $request->page);
            // }

            $result = [
                'data' => $sap_compliances->items(),
                'per_page' => $sap_compliances->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $sap_compliances->currentPage(),
                'last_page' => $sap_compliances->lastPage(),
                'total' => $sap_compliances->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createSapComplianceFormExcel()
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
                    'unit_code' => 1, // Thay đổi cột 'unit_id' thành 'unit_code'
                    'bar_code' => 2,
                    'name' => 3,
                    'quy_cach' => 4,
                    'check_qc' => 5,
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    $unit = SapUnit::where('unit_code', $row[$template_structure['unit_code']])->first();

                    if ($unit) {
                        $check_qc = $row[$template_structure['check_qc']];

                        $sapCompliance = SapCompliance::updateOrCreate(
                            [
                                'sap_code' => $row[$template_structure['sap_code']],
                                'unit_id' => $unit->id, // Sử dụng 'id' của bảng 'unit'
                            ],
                            [
                                'bar_code' => $row[$template_structure['bar_code']],
                                'name' => $row[$template_structure['name']],
                                'quy_cach' => $row[$template_structure['quy_cach']],
                                'check_qc' => $check_qc === null ? 1 : 0,
                            ]
                        );

                        $result->push($sapCompliance);
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
            $query = SapCompliance::query();

            if ($this->request->filled('bar_codes')) {
                $query->whereIn('bar_code', $this->request->bar_codes);
            }
            if ($this->request->filled('sap_codes')) {
                $query->whereIn('sap_code', $this->request->sap_codes);
            }

            if ($this->request->filled('unit_ids')) {
                $query->whereIn('unit_id', $this->request->unit_ids);
            }

            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }

            if ($this->request->filled('id')) {
                $query->where('id', $this->request->id);
            }


            $sap_compliances = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Mã sản phẩm');
            $sheet->setCellValue('B1', 'Mã Barcode');
            $sheet->setCellValue('C1', 'Đơn vị tính');
            $sheet->setCellValue('D1', 'Tên sản phẩm');
            $sheet->setCellValue('E1', 'Quy cách');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($sap_compliances as $sap_compliance) {
                $sheet->setCellValue('A' . $row, $sap_compliance->sap_code);
                $sheet->setCellValue('B' . $row, $sap_compliance->bar_code);
                $sheet->setCellValue('C' . $row, $sap_compliance->unit->unit_code);
                $sheet->setCellValue('D' . $row, $sap_compliance->name);
                $sheet->setCellValue('E' . $row, $sap_compliance->quy_cach);
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
            $filename = 'sap_compliances.xlsx';

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
    public function createNewSapCompliance()
    {
        try {
            $validator = Validator::make($this->data, [
                'sap_code' => 'required|string',
                'unit_id' => 'required|integer|exists:sap_units,id',
                'name' => 'required|string',
                'bar_code' => 'nullable|string',
                'quy_cach' => 'nullable|string',
                'check_qc' => 'nullable|in:0,1',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã material.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là số nguyên.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
                'bar_code.string' => 'Mã Barcode phải là chuỗi.',
                'quy_cach.string' => 'Quy cách phải là chuỗi.',
                'check_qc.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            } else {
                $sapCompliance = SapCompliance::create([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'bar_code' => $this->data['bar_code'],
                    'name' => $this->data['name'],
                    'quy_cach' => $this->data['quy_cach'],
                    'check_qc' => $this->data['check_qc'],
                ]);

                return $sapCompliance;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
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
                    $exist_sap_material = SapCompliance::query()
                        ->where('sap_code', $material['code'])
                        ->where('unit_id', $unit->id)->first();

                    if ($material['status'] == "delete") {
                        $exist_sap_material->is_deleted = true;
                        $exist_sap_material->save();
                    } {
                        if ($exist_sap_material) {
                            if ($exist_sap_material->bar_code == "") {
                                $exist_sap_material->update([
                                    'name' => $material['name'],
                                    'bar_code' => $material['bar_code'],
                                ]);
                            } else {
                                $exist_sap_material->update([
                                    'name' => $material['name'],
                                    //  'bar_code' => $material['bar_code'],
                                ]);
                            }

                            $result['update_count']++;
                        } else {
                            SapCompliance::create([
                                'unit_id' => $unit->id,
                                'sap_code' => $material['code'],
                                'name' => $material['name'],
                                'bar_code' => $material['bar_code'], // Thêm trường mới
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
    public function updateExistingSapCompliance($id)
    {
        try {
            $validator = Validator::make($this->data, [

                'sap_code' => 'string:sap_materials,sap_code',
                'unit_id' => 'integer|exists:sap_units,id',
                // 'bar_code' => 'string',
                'name' => 'required|string',
                'quy_cach' => 'nullable|string',
                'check_qc' => 'nullable|in:0,1',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã SAP.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                //'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là chuỗi.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                // 'bar_code.string' => 'Mã Barcode phải là chuỗi.',
                'name.required' => 'Yêu cầu nhập tên SAP.',
                'name.string' => 'Tên SAP phải là chuỗi.',
                'quy_cach.string' => 'Quy cách phải là chuỗi.',
                'check_qc.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            } else {

                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }

                $sapCompliance = SapCompliance::findOrFail($id);
                $sapCompliance->fill([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    // 'bar_code' => $this->data['bar_code'],
                    'name' => $this->data['name'],
                    'quy_cach' => $this->data['quy_cach'],
                    'check_qc' => $this->data['check_qc'],
                ]);
                $sapCompliance->save();

                return $sapCompliance;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSapCompliance($id)
    {
        try {
            $sapCompliance = SapCompliance::findOrFail($id);
            $sapCompliance->delete();
            return $sapCompliance;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
