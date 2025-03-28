<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerPartner;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use App\Models\Master\CustomerGroup;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class CustomerPartnerRepository extends RepositoryAbs
{
    public function getAvailableCustomerPartners($is_minified)
    {
        try {
            $query = CustomerPartner::query()->with(['customer_partner_stores']);

            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                if (!is_array($customer_group_ids)) {
                    $customer_group_ids = explode(',', $customer_group_ids);
                }
                $query->whereIn('customer_group_id', $customer_group_ids);
            }
            if ($is_minified) {
                $query->select('id', 'code', 'name', 'note');
            }
            $query->with([
                'customer_group' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ]);
            $perPage = $this->request->filled('per_page') ? $this->request->per_page : 500;
            $customer_partners = $query->paginate($perPage, ['*'], 'page', $this->request->page);



            $result = [
                'data' => $customer_partners->items(),
                'per_page' => $customer_partners->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $customer_partners->currentPage(),
                'last_page' => $customer_partners->lastPage(),
                'total' => $customer_partners->total(),
            ];

            // Retrieve the customer partners again, this time ordering by 'id' in descending order
            $customer_partners = $query->orderBy('id', 'desc')->get();

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getCustomerPartnerById($id)
    {
        try {
            $customer_partners = CustomerPartner::find($id);
            return $customer_partners;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createCustomerPartnerFormExcel()
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
            }

            $file = $this->request->file('file');
            $excel_extractor = new ExcelExtractor();
            $data = $excel_extractor->extractData($file);
            $template_structure = [
                'customer_group_name' => 0,
                'name' => 1,
                'code' => 2,
                'note' => 3,
                'LV2' => 4,
                'LV3' => 5,
                'LV4' => 6,
            ];
            $result = [];

            DB::beginTransaction(); // Start a database transaction

            foreach ($data as $row) {
                $customer_group_name = $row[$template_structure['customer_group_name']];
                $customer_group = CustomerGroup::where('name', $customer_group_name)->first();
                if (!$customer_group) {
                    $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customer_group_name;
                    continue;
                }

                $customer_partner_name = $row[$template_structure['name']];
                $existingCustomerPartner = CustomerPartner::where('name', $customer_partner_name)
                    ->where('customer_group_id', $customer_group->id)
                    ->first();

                if ($existingCustomerPartner) {
                    // Khách hàng đã tồn tại, thực hiện cập nhật dữ liệu
                    $existingCustomerPartner->code = $row[$template_structure['code']];
                    $existingCustomerPartner->note = $row[$template_structure['note']];
                    $existingCustomerPartner->LV2 = $row[$template_structure['LV2']];
                    $existingCustomerPartner->LV3 = $row[$template_structure['LV3']];
                    $existingCustomerPartner->LV4 = $row[$template_structure['LV4']];
                    $existingCustomerPartner->save();

                    $result[] = $existingCustomerPartner;
                } else {
                    // Khách hàng chưa tồn tại, thực hiện tạo mới
                    $customer_partner_data = [
                        'code' => $row[$template_structure['code']],
                        'name' => $customer_partner_name,
                        'note' => $row[$template_structure['note']],
                        'LV2' => $row[$template_structure['LV2']],
                        'LV3' => $row[$template_structure['LV3']],
                        'LV4' => $row[$template_structure['LV4']],
                        'customer_group_id' => $customer_group->id,
                    ];

                    $customer_partner = CustomerPartner::create($customer_partner_data);
                    $result[] = $customer_partner;
                }
            }

            DB::commit(); // Commit the database transaction

            return [
                'created_list' => $result,
                'errors' => $this->errors,
            ];
        } catch (\Exception $exception) {
            DB::rollBack(); // Roll back the database transaction in case of an exception
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function exportToExcel()
    {
        try {
            $query = CustomerPartner::query();

            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
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

            $customer_partners = $query->orderBy('id', 'desc')->get();

            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột
            $sheet->setCellValue('A1', 'Nhóm khách hàng');
            $sheet->setCellValue('B1', 'Khác hàng key');
            $sheet->setCellValue('C1', 'Mã khách hàng');
            $sheet->setCellValue('D1', 'Ghi chú');
            $sheet->setCellValue('E1', 'LV2');
            $sheet->setCellValue('F1', 'LV3');
            $sheet->setCellValue('G1', 'LV4');

            // Ghi dữ liệu vào file Excel
            $row = 2;
            foreach ($customer_partners as $partner) {
                $sheet->setCellValue('A' . $row, $partner->customer_group->name);
                $sheet->setCellValue('B' . $row, $partner->name);
                $sheet->setCellValue('C' . $row, $partner->code);
                $sheet->setCellValue('D' . $row, $partner->note); // Thay đổi thành cột số lượng SKU KH
                $sheet->setCellValue('E' . $row, $partner->LV2);
                $sheet->setCellValue('F' . $row, $partner->LV3);
                $sheet->setCellValue('G' . $row, $partner->LV4);
                $row++;
            }

            // Tự căn chỉnh kích thước các cột dựa trên độ dài ký tự của dữ liệu
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
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

            $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

            // Tạo đối tượng Writer để ghi file Excel
            $writer = new Xlsx($spreadsheet);

            // Đặt tên file và định dạng
            $filename = 'customer_partners.xlsx';

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

    public function createNewCustomerPartner()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required|integer|exists:customer_groups,id',
                'code' => 'required|string',
                'name' => 'string',
                // 'note' => 'string',
                'LV2' => 'string',
                'LV3' => 'string',
                'LV4' => 'string',
            ], [
                'customer_group_id.required' => 'Yêu cầu chọn nhóm khách hàng.',
                'customer_group_id.integer' => 'ID nhóm khách hàng phải là số nguyên.',
                'customer_group_id.exists' => 'ID nhóm khách hàng không tồn tại.',
                'code.required' => 'Yêu cầu nhập mã khách hàng.',
                'code.string' => 'Mã khách hàng phải là chuỗi.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                // 'note.string' => 'Ghi chú phải là chuỗi.',
                'LV2.string' => 'LV2 phải là chuỗi.',
                'LV3.string' => 'LV3 phải là chuỗi.',
                'LV4.string' => 'LV4 phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            }

            $customerGroup = CustomerGroup::find($this->data['customer_group_id']);
            if (!$customerGroup) {
                $this->errors['customer_group_id'] = 'ID nhóm khách hàng không tồn tại.';
                return false;
            }

            // Kiểm tra xem đã tồn tại mã code nào được gắn với tên này trong nhóm khách hàng hay chưa
            $existingCustomerPartner = CustomerPartner::where('customer_group_id', $this->data['customer_group_id'])
                ->where('name', $this->data['name'])
                ->first();

            if ($existingCustomerPartner) {
                $this->errors['name'] = 'Tên khách hàng đã được gắn với một mã code khác trong nhóm này.';
                return false;
            }

            $this->data['is_deleted'] = false;
            $customerPartner = CustomerPartner::create($this->data);

            return $customerPartner;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    // public function updateOrInsert()
    // {
    //     foreach ($this->data as $customer_partner) {
    //         $validator = Validator::make($customer_partner, [
    //             'code' => 'required|string',
    //         ], [
    //             'code.required' => 'Yêu cầu nhập mã kho.',
    //         ]);

    //         if ($validator->fails()) {
    //             $this->errors = $validator->errors()->all();
    //         } else {
    //             $exist_customer_partner = CustomerPartner::where('code', $customer_partner['code'])->first();
    //             if ($exist_customer_partner) {
    //                 $exist_customer_partner->update([
    //                     'name' => $customer_partner['name'],
    //                     'code' => $customer_partner['code'] ?? '',
    //                     'note' => $customer_partner['note'] ?? '',
    //                 ]);
    //             } else {
    //                 CustomerPartner::create([
    //                     'code' => $customer_partner['code'],
    //                     'name' => $customer_partner['name'],
    //                     'note' => $customer_partner['note'] ?? '',
    //                 ]);
    //             }
    //         }
    //     }
    // }
    public function updateExistingCustomerPartner($id)
    {
        try {
            $customer_partner = CustomerPartner::where('id', $id)->firstOrFail();

            $validator = Validator::make($this->data, [
                'code' => 'required|string',
                'name' => 'string',
                // 'note' => 'string',
                // 'LV2' => 'string',
                // 'LV3' => 'string',
                // 'LV4' => 'string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                // 'note.string' => 'Ghi chú phải là chuỗi.',
                // 'LV2.string' => 'LV2 phải là chuỗi.',
                // 'LV3.string' => 'LV3 phải là chuỗi.',
                // 'LV4.string' => 'LV4 phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $customer_partner => $validator) {
                    if ($errors->has($customer_partner)) {
                        $this->errors[$customer_partner] = $errors->first($customer_partner);
                        return false;
                    }
                }
            } else {
                $customerGroup = CustomerGroup::find($this->data['customer_group_id']);
                if (!$customerGroup) {
                    $this->errors['customer_group_id'] = 'ID nhóm khách hàng không tồn tại.';
                    return false;
                }

                // Kiểm tra xem đã tồn tại mã code nào được gắn với tên này trong nhóm khách hàng hay chưa
                // $existingCustomerPartner = CustomerPartner::where('customer_group_id', $this->data['customer_group_id'])
                //     ->where('name', $this->data['name'])
                //     ->where('id', '!=', $id) // Loại trừ bản ghi hiện tại để tránh xung đột
                //     ->first();

                // if ($existingCustomerPartner) {
                //     $this->errors['name'] = 'Tên khách hàng đã được gắn với một mã code khác trong nhóm này.';
                //     return false;
                // }

                $customer_partner->update($this->data);
                return $customer_partner;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function deleteExistingCustomerPartner($id)
    {
        try {
            $customer_partner = CustomerPartner::findOrFail($id);
            $customer_partner->delete();
            return $customer_partner;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
