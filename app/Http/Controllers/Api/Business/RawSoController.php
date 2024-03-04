<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;

class RawSoController extends ResponseController
{
    public function getRawSoHeaders(Request $request)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->getRawSoHeaders();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getRawSoHeadersToPushSap(Request $request)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->getRawSoHeaders(true);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getRawSoHeaderById(Request $request, $id)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->getRawSoHeaderById($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function createPromotiveRawSoHeader(Request $request)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->createPromotiveRawSoHeader();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteRawSoHeader(Request $request, $id)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->deleteRawSoHeader($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function copyRawSoItem(Request $request)
    {
        $id = $request->input('raw_so_item_id'); // Lấy giá trị raw_so_item_id từ request

        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->copyRawSoItem($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteRawSoItem(Request $request, $id)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->deleteRawSoItem($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function addRawSoItemToRawSoHeader(Request $request)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->addRawSoItemToRawSoHeader();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateRawSoHeader(Request $request, $id)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->updateRawSoHeader($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function syncRawSoHeaderFromSap(Request $request)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->syncRawSoHeaderFromSap();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function makeRawSoHeadersWatingToSync(Request $request)
    {
        $handler = BusinessRepository::rawSoHeaderRequest($request);
        $data = $handler->makeRawSoHeadersWatingToSync();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
