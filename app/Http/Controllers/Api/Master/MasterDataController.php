<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class MasterDataController extends ResponseController
{
    public function sysCategory(Request $request, $category)
    {
        switch ($category) {
            case '1':
                $handler = MasterRepository::customerRequest($request);
                $customers = $handler->getAvailableCustomers();

                // Kiểm tra xem $customers có phải là một mảng hay không
                if (!is_array($customers)) {
                    $customers = $customers->toArray();
                }

                $customer = $handler->createNewCustomer();
                $customer = $handler->updateExistingCustomer();

                //lấy ra các thuộc tính cần lấy
                $filteredCustomers = array_map(function ($customer) {
                    return [
                        'code' => $customer['code'],
                        'name' => $customer['name'],
                        'email' => $customer['email'],
                        'phone_number' => $customer['phone_number'],
                        'address' => $customer['address'],
                    ];
                }, $customers);
                return $this->responseSuccess($filteredCustomers);
                break; // Thêm break vào đây
            case '2':
                $handler = MasterRepository::distributionChannelRequest($request);
                $distributionChannels = $handler->getAvailableDistributionChannels();
                $distributionChannel = $handler->createNewDistributionChannel();
                //$distributionChannel = $handler->updateExistingDistributionChannel($id);
                //lấy ra các thuộc tính cần lấy
                $filteredDistributionChannels = array_map(function ($distributionChannel) {
                    return [
                        'code' => $distributionChannel['code'],
                        'name' => $distributionChannel['name'],
                        'api_url' => $distributionChannel['api_url'],
                        'api_key' => $distributionChannel['api_key'],
                        'api_secret' => $distributionChannel['api_secret'],
                    ];
                }, $distributionChannels);
                return $this->responseSuccess($filteredDistributionChannels);
                break;            
            default:
                return $this->responseError($handler->getMessage('Invalid category'), $handler->getErrors());
                break;            
        }
    }
}