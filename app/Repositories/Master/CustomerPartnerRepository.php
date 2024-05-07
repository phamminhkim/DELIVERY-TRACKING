<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerPartner;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use App\Models\Master\CustomerGroup;


class CustomerPartnerRepository extends RepositoryAbs
{
    public function getAvailableCustomerPartners($is_minified)
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
                    $this->errors[] = 'Tên khách hàng ' . $customer_partner_name . ' đã tồn tại trong nhóm này.';
                    continue;
                }

                $customer_partner_data = [
                    'code' => $row[$template_structure['code']],
                    'name' => $customer_partner_name,
                    'note' => $row[$template_structure['note']],
                    'LV2' => $row[$template_structure['LV2']],
                    'LV3' => $row[$template_structure['LV3']],
                    'LV4' => $row[$template_structure['LV4']],
                    'customer_group_id' => $customer_group->id,
                ];

                $customer_partner = CustomerPartner::updateOrCreate(
                    ['code' => $customer_partner_data['code']],
                    $customer_partner_data
                );

                $result[] = $customer_partner;
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

    public function createNewCustomerPartner()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required|integer|exists:customer_groups,id',
                'code' => 'required|string',
                'name' => 'string',
                'note' => 'string',
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
                'note.string' => 'Ghi chú phải là chuỗi.',
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
                'note' => 'string',
                'LV2' => 'string',
                'LV3' => 'string',
                'LV4' => 'string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'note.string' => 'Ghi chú phải là chuỗi.',
                'LV2.string' => 'LV2 phải là chuỗi.',
                'LV3.string' => 'LV3 phải là chuỗi.',
                'LV4.string' => 'LV4 phải là chuỗi.',
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
                $existingCustomerPartner = CustomerPartner::where('customer_group_id', $this->data['customer_group_id'])
                    ->where('name', $this->data['name'])
                    ->where('id', '!=', $id) // Loại trừ bản ghi hiện tại để tránh xung đột
                    ->first();

                if ($existingCustomerPartner) {
                    $this->errors['name'] = 'Tên khách hàng đã được gắn với một mã code khác trong nhóm này.';
                    return false;
                }

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
