<?php

namespace App\Repositories\Master;

use App\CustomerPartnerStore;
use App\Models\Master\Company;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookStoreRepository extends RepositoryAbs
{
    public function getAllBookStore()
    {
        try {
            $query = CustomerPartnerStore::query();
            $customer_partner_store = $query->get();

            return $customer_partner_store;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createBookStore($data)
    {
        try {
            $data_client = $this->request->all();
            $validator = Validator::make($data, [
                'code' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            } else {
                $create_customer = CustomerPartnerStore::create([
                    'code' => $data_client['code'],
                    'name' => $data_client['name'],
                ]);
            }
            return $create_customer;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateBookStore($data, $id)
    {
        try {
            $data_client = $this->request->all();
            $validator = Validator::make($data, [
                'code' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            } else {
                $update_customer = CustomerPartnerStore::where('id', $id)->update([
                    'code' => $data_client['code'],
                    'name' => $data_client['name'],
                ]);
            }
            return $update_customer;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteBookStore($data, $id)
    {
        try {
            $delete_customer = CustomerPartnerStore::where('id', $id)->delete();
            return $delete_customer;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function toolCreateBookStore($data)
    {
        try {
            $data_client = $this->request->all(); // Lấy dữ liệu từ API request
            $insertedRecords = []; // Mảng chứa các record đã thêm

            foreach ($data_client as $item) {
                // Validate từng bản ghi
                $validator = Validator::make($item, [
                    'Tên nhà sách' => 'required|string',
                    'ten_ns' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    // Nếu không hợp lệ, bỏ qua và ghi log lỗi nếu cần
                    continue;
                }

                // Thêm bản ghi vào database
                $insertedRecord = CustomerPartnerStore::create([
                    'code' => $item['ten_ns'], // Nếu `ten_ns` null, dùng giá trị rỗng
                    'name' => $item['Tên nhà sách'],
                ]);

                $insertedRecords[] = $insertedRecord; // Lưu bản ghi đã thêm vào mảng
            }

           return $insertedRecords; // Trả về mảng chứa các bản ghi đã thêm
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
