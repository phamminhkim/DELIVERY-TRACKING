<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Batch;
use App\Models\Business\ConvertTableConfig;
use App\Models\Business\ExtractDataConfig;
use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\FileExtractError;
use App\Models\Business\FileExtractErrorLog;
use App\Models\Business\Order;
use App\Models\Business\RawExtractHeader;
use App\Models\Business\RawExtractItem;
use App\Models\Business\RawSoHeader;
use App\Models\Business\RawSoItem;
use App\Models\Business\RestructureDataConfig;
use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerGroup;
use App\Models\Master\UserMorph;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract;

class AiController extends ResponseController
{
    public function extractOrder(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->extractOrder();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function extractOrderDirect(Request $request)
    {
        $handler = BusinessRepository::aiRequestDirect($request);
        $data = $handler->extractOrderDirect();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function extractOrderFromUploadedFile(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->extractOrderFromUploadedFile($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }


    public function reconvertUploadedFile(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->reconvertUploadedFile($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function clean(Request $request)
    {
        Schema::disableForeignKeyConstraints();
        CustomerGroup::query()->truncate();
        Batch::query()->truncate();
        ExtractOrderConfig::query()->truncate();
        ExtractDataConfig::query()->truncate();
        ConvertTableConfig::query()->truncate();
        RestructureDataConfig::query()->truncate();
        FileExtractError::query()->truncate();
        FileExtractErrorLog::query()->truncate();
        RawExtractHeader::query()->truncate();
        RawExtractItem::query()->truncate();
        RawSoHeader::query()->truncate();
        RawSoItem::query()->truncate();
        UploadedFile::query()->truncate();
        UserMorph::where('morphable_type', 'App\Models\Business\UploadedFile')->delete();
        Schema::enableForeignKeyConstraints();
    }
    public function extractDataForConfig(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->extractDataForConfig();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function convertToTableForConfig(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->convertToTableForConfig();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function restructureDataForConfig(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->restructureDataForConfig();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getExtractOrderConfigs(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->getExtractOrderConfigs();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function createExtractOrderConfigs(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->createExtractOrderConfigs();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateExtractOrderConfig(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->updateExtractOrderConfig($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getConvertConfigList(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->getConvertConfigList();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateConvertConfig(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->updateConvertConfig($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteConvertConfig(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->deleteConvertConfig($id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function findTextPosition(Request $request)
    {
        $handler = BusinessRepository::pdfTextLocator($request);
        $data = $handler->findTextPosition();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getTextByCoords(Request $request)
    {
        $handler = BusinessRepository::pdfTextLocator($request);
        $data = $handler->getTextByCoords();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getFullText(Request $request)
    {
        $handler = BusinessRepository::pdfTextLocator($request);
        $data = $handler->getFullText();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function checkStringKey(Request $request)
    {
        $handler = BusinessRepository::pdfTextLocator($request);
        $data = $handler->checkStringKey();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function checkStringKeyMultiFiles(Request $request)
    {
        $handler = BusinessRepository::pdfTextLocator($request);
        $data = $handler->checkStringKeyMultiFiles();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
