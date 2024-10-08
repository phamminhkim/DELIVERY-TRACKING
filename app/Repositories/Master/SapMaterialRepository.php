<?php

namespace App\Repositories\Master;

use App\Models\Master\SapMaterial;
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


class SapMaterialRepository extends RepositoryAbs
{
    public function getAvailableSapMaterials($is_minified, $request)
    {
        try {
            $query = SapMaterial::query();

            if ($request->filled('search')) {
                $query->with(['unit']);
                $query->limit(50);
            }

            if ($request->filled('bar_codes')) {
                $query->whereIn('bar_code', $request->bar_codes);
            }
            if ($request->filled('sap_codes')) {
                $query->whereIn('sap_code', $request->sap_codes);
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

            if ($request->filled('search') && $request->search != null && $request->search != 'undefined') {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('sap_code', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%")
                        ->orWhere('bar_code', 'like', "%$search%");
                });
            }
            if ($is_minified) {
                $query->select('id', 'name', 'sap_code', 'unit_id', 'bar_code');
            }
            $query->where('is_deleted', 0);
            $query->with(['unit' => function ($query) {
                $query->select(['id', 'unit_code']);
            }]);

            $perPage = $request->filled('per_page') ? $this->request->per_page : 1000;
            $sap_materials = $query->paginate($perPage, ['*'], 'page', $request->page);

            if ($request->filled('search') && $sap_materials->isEmpty()) {
                $sap_materials = $query->paginate($perPage, ['*'], 'page', $request->page);
            }

            $result = [
                'data' => $sap_materials->items(),
                'per_page' => $sap_materials->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $sap_materials->currentPage(),
                'last_page' => $sap_materials->lastPage(),
                'total' => $sap_materials->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function exportToExcel()
    {
        try {
            $query = SapMaterial::query();

            if ($this->request->filled('search')) {
                $query->with(['unit']);
                $query->limit(50);
            }

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


            $sap_matearials = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Mã sản phẩm');
            $sheet->setCellValue('B1', 'Mã Barcode');
            $sheet->setCellValue('C1', 'Đơn vị tính');
            $sheet->setCellValue('D1', 'Tên sản phẩm');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($sap_matearials as $sap_matearial) {
                $sheet->setCellValue('A' . $row, $sap_matearial->sap_code);
                $sheet->setCellValue('B' . $row, $sap_matearial->bar_code);
                $sheet->setCellValue('C' . $row, $sap_matearial->unit->unit_code);
                $sheet->setCellValue('D' . $row, $sap_matearial->name);
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
            $filename = 'sap_materials.xlsx';

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
    public function createSapMaterialFormExcel()
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
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    $unit = SapUnit::where('unit_code', $row[$template_structure['unit_code']])->first();

                    if ($unit) {
                        $sapMaterial = SapMaterial::updateOrCreate(
                            [
                                'sap_code' => $row[$template_structure['sap_code']],
                                'unit_id' => $unit->id, // Sử dụng 'id' của bảng 'unit'
                            ],
                            [
                                'bar_code' => $row[$template_structure['bar_code']],
                                // Thêm các trường dữ liệu khác tương ứng
                            ]
                        );

                        $result->push($sapMaterial);
                    }
                }

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
    public function createNewSapMaterial()
    {
        try {
            $validator = Validator::make($this->data, [
                'sap_code' => 'required|string',
                'unit_id' => 'required|integer|exists:sap_units,id',
                'name' => 'required|string',
                'bar_code' => 'string',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã material.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là chuỗi.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
                'bar_code.string' => 'Mã Barcode phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            } else {
                $sapMaterial = SapMaterial::create([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'bar_code' => $this->data['bar_code'],
                    'name' => $this->data['name'],
                ]);

                if (isset($this->data['priority'])) {
                    // Cách 1: Sử dụng phương thức setAttribute
                    $sapMaterial->setAttribute('priority', $this->data['priority']);

                    // Cách 2: Thiết lập trực tiếp trường 'priority' trong mảng attributes
                    // $sapMaterial->attributes['priority'] = $this->data['priority'];
                }

                return $sapMaterial;
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
                    $exist_sap_material = SapMaterial::query()
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
                            SapMaterial::create([
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
    public function updateExistingSapMaterial($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'sap_code' => 'string:sap_materials,sap_code',
                'unit_id' => 'integer|exists:sap_units,id',
                'name' => 'required|string',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã SAP.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'unit_id.integer' => 'Mã unit phải là số nguyên.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên SAP.',
                'name.string' => 'Tên SAP phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $SapMaterial => $validator) {
                    if ($errors->has($SapMaterial)) {
                        $this->errors[$SapMaterial] = $errors->first($SapMaterial);
                        return false;
                    }
                }
            } else {
                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }

                $sapMaterial = SapMaterial::findOrFail($id);
                $sapMaterial->fill([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'name' => $this->data['name'],
                    // 'priority' => $this->data['priority'],
                ]);

                // Check if 'bar_code' key exists before updating
                if (array_key_exists('bar_code', $this->data)) {
                    $sapMaterial->bar_code = $this->data['bar_code'];
                }
                // Check if 'bar_code' key exists before updating
                if (array_key_exists('priority', $this->data)) {
                    $sapMaterial->priority = $this->data['priority'];
                }
                $sapMaterial->save();

                return $sapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSapMaterial($id)
    {
        try {
            $sapMaterial = SapMaterial::findOrFail($id);
            $sapMaterial->delete();
            return $sapMaterial;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
