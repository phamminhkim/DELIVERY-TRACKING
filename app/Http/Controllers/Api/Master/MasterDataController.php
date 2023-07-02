<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class MasterDataController extends ResponseController
{
    public function syncCategory(Request $request, $category)
    {
        switch ($category) {
            case SapSyncCategory::Customer:
                $handler = MasterRepository::customerRequest($request);
                $customers = $handler->getAvailableCustomers();

                // Kiểm tra xem $customers có phải là một mảng hay không
                if (!is_array($customers)) {
                    $customers = $customers->toArray();
                }
                $customer = $handler->createNewCustomer();
                $customer = $handler->updateExistingCustomer();

                //lấy ra các thuộc tính cần lấy
                $filtered_customers = array_map(function ($customer) {
                    return [
                        'code' => $customer['code'],
                        'name' => $customer['name'],
                        'email' => $customer['email'],
                        'phone_number' => $customer['phone_number'],
                        'address' => $customer['address'],
                    ];
                }, $customers);
                return $this->responseSuccess($filtered_customers);

            case SapSyncCategory::DistributionChannel:
                $handler = MasterRepository::distributionChannelRequest($request);
                $distribution_channels = $handler->getAvailableDistributionChannels();
                $distribution_channel = $handler->createNewDistributionChannel();
                //$distributionChannel = $handler->updateExistingDistributionChannel($id);
                //lấy ra các thuộc tính cần lấy
                $filtered_distribution_channels = array_map(function ($distribution_channel) {
                    return [
                        'code' => $distribution_channel['code'],
                        'name' => $distribution_channel['name'],
                        'api_url' => $distribution_channel['api_url'],
                        'api_key' => $distribution_channel['api_key'],
                        'api_secret' => $distribution_channel['api_secret'],
                    ];
                }, $distribution_channels);
                return $this->responseSuccess($filtered_distribution_channels);

            default:
                return $this->responseError('Invalid category', []);
        }
    }
}
