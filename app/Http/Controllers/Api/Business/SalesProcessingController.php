<?php

namespace App\Http\Controllers\Api\Business;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\BaseController\ResponseController;
use App\Imports\SalesImport;
use App\Mail\TestMail;
use App\OrderProcessSale;
use App\Repositories\BusinessRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Swift_Attachment;

class SalesProcessingController extends ResponseController
{
    public function importSales(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file.*' => 'required|mimes:xlsx,xls,csv|max:20480'
        ]);
        if ($validator->fails()) {
            return $this->responseError('Validation Error', $validator->errors());
        }
        $arr = [];
        $files = $request->file('file');
        foreach ($files as $file) {
            $import = new SalesImport;
            Excel::import($import, $file);
            // dd($import->getData(),'123');
            $arr[] = $import->getData();
            if (count($import->getData()['items']) == 0) {
                return response()->json([
                    'success' => false,
                    'data' => $import->getData(),
                    'message' => 'Không đọc được dữ liệu. Vui lòng kiểm tra lại file'
                ]);
            }
        }
        // dd($import->getData(),'123');
        // if ($import->failures()->isNotEmpty()) {
        //     return $this->responseError('Import Error', $import->failures());
        // }
        return response()->json([
            'success' => true,
            // 'data' => $import->getData(),
            'data' => $arr,
            'message' => 'Chuyển đổi dữ liệu thành công'
        ]);

        // return $this->responseSuccess('Import Success');
    }
    public function checkSapCode(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->checkMaterialSap($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function saveSales(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->saveSales($request->items);
        if ($data !== false) {
            $item_save = $data['orderProcessSaleItems']->toArray();
            $auth_user = auth()->user();
            $id = $data['id'];
            // Gửi mail
            if ($data['status'] !== 'pending') {
                $details = [
                    'title' => 'Mail Gửi điều phối',
                    'body' => 'Gửi đơn hàng cho điều phối, kèm theo danh sách các mục.',
                    'items' => $item_save,
                    'user' => $auth_user,
                    'data' => $data

                ];
                // Tạo file Excel
                $fileName = 'don_hang.xlsx';
                Excel::store(new OrderExport($details), $fileName, 'local');
                $filePath = storage_path('app/order/' . $fileName);
                if (!file_exists($filePath)) {
                    throw new \Exception("File không tồn tại tại: " . $filePath);
                }
                $so_header = $request->so_header;
                $central = 'Trung tâm: ' . $so_header['central_branch'];
                $note = 'Ghi chú: ' . $so_header['description'];
                $link = url('v2-order-processes');
                $body = '<div><h2>Đơn hàng</h2><p>' . $central . '</p><p>' . $note . '</p></div>';
                $body .= '<div><a href="' . $link . '">Link xử lý đơn hàng</a></div>';
                $subject = '✨ (Mới) Đơn hàng #' . $data['code'] . ' - ' . $data['created_at'];
                // Gửi email
                // '<img src="https://delivery.thienlong.vn/icon_tl.png" width="5%" style="margin-right: 10px;" />'.
                Mail::send('emails.test', $details, function ($message) use ($details, $filePath, $so_header, $body, $id, $subject, $auth_user) {
                    $message->to($auth_user['email'])
                        // ->subject($so_header['title'])
                        ->subject($subject, 'text/html')
                        ->setBody($body, 'text/html')
                        ->attach(
                            Swift_Attachment::fromPath($filePath)->setFilename('don_hang.xlsx')
                        );
                });
            }
            return $this->responseSuccess($item_save);

        }

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }

    public function updateSaveSales(Request $request, $id)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->updateSaveSales($request->items, $id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function deleteOrderPOSales(Request $request, $id)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->deleteOrderPOSales($request->items, $id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }

    public function checkBookStore(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->checkBookStore($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function checkSapCompliance(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->checkSapCompliance($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function processingSOData(Request $request)
    {
        $bussiness = BusinessRepository::processingSOData($request);
        $data = $bussiness->processingSOData($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function getStatusPending(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->getStatusPending($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function getAllOrderPOSales(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->getAllOrderPOSales($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function getAllOrderPOSalesCreateBy(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->getAllOrderPOSalesCreateBy($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function getAllOrderPOSalesCreateByID(Request $request, $id)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->getAllOrderPOSalesCreateByID($request->items, $id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function applyOrderPOsales(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->applyOrderPOsales($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function sendingOrderPOsales(Request $request, $id)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->sendingOrderPOsales($request->items, $id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function getOrderStatusStatics(Request $request)
    {
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->getOrderStatusStatics($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
}
