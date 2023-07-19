<?php

namespace App\Repositories\Master;

use App\Models\Master\OrderReviewOption;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class OrderReviewOptionRepository extends RepositoryAbs
{
    public function getAvailableOrderReviewOptions()
    {
        try {
            $orderReviewOptions = OrderReviewOption::all();
            return $orderReviewOptions;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewOrderReviewOption()
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string',

            ], [
                'name.required' => 'Yêu cầu nhập tên Order Review Option .',
                'name.string' => 'Tên Order Review Option phải là chuỗi.',


            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $orderReviewOption => $validator) {
                    if ($errors->has($orderReviewOption)) {
                        $this->errors[$orderReviewOption] = $errors->first($orderReviewOption);
                        return false;
                    }
                }
            } else {
                $orderReviewOption = OrderReviewOption::create($this->data);

                return $orderReviewOption;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function updateExistingOrderReviewOption($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string',
            ], [
                'name.required' => 'Yêu cầu nhập tên Order Review Option .',
                'name.string' => 'Tên Order Review Option phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $orderReviewOption => $validator) {
                    if ($errors->has($orderReviewOption)) {
                        $this->errors[$orderReviewOption] = $errors->first($orderReviewOption);
                        return false;
                    }
                }
            } else {
                $orderReviewOption = OrderReviewOption::findOrFail($id);
                $orderReviewOption->update($this->data);
                return $orderReviewOption;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingOrderReviewOption($id)
    {
        try {
            $orderReviewOption = OrderReviewOption::findOrFail($id);
            $orderReviewOption->delete();
            return $orderReviewOption;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
