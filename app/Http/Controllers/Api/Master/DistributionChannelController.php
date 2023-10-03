<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class DistributionChannelController extends ResponseController
{
    public function getAvailableDistributionChannels(Request $request)
    {

        $handler = MasterRepository::distributionChannelRequest($request);
        $distributionChannels = $handler->getAvailableDistributionChannels(false);

        if ($distributionChannels) {
            return $this->responseSuccess($distributionChannels);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableDistributionChannelsActive(Request $request)
    {

        $handler = MasterRepository::distributionChannelRequest($request);
        $distributionChannels = $handler->getAvailableDistributionChannels(true);

        if ($distributionChannels) {
            return $this->responseSuccess($distributionChannels);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewDistributionChannel(Request $request)
    {

        $handler = MasterRepository::distributionChannelRequest($request);
        $distributionChannel = $handler->createNewDistributionChannel();

        if ($distributionChannel) {
            return $this->responseSuccess($distributionChannel);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingDistributionChannel(Request $request, $id)
    {
        $handler = MasterRepository::distributionChannelRequest($request);
        $distributionChannel = $handler->updateExistingDistributionChannel($id);

        if ($distributionChannel) {
            return $this->responseSuccess($distributionChannel);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingDistributionChannel(Request $request, $id)
    {
        $handler = MasterRepository::distributionChannelRequest($request);
        $is_success = $handler->deleteExistingDistributionChannel($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
