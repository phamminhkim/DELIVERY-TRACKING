<?php

namespace App\Repositories;

use App\Repositories\Business\AiRepository;
use App\Repositories\Business\ApplicationRepository;
use App\Repositories\Business\DashboardRepository;
use App\Repositories\Business\OrderRepository;
use App\Repositories\Business\DeliveryRepository;
use App\Services\DataRestructureService;
use App\Services\LocalFileService;
use App\Services\PdfDataExtractorService;
use Illuminate\Http\Request;

class BusinessRepository
{
    public static function applicationRequest(Request $request)
    {
        return new ApplicationRepository($request);
    }
    public static function orderRequest(Request $request)
    {
        return new OrderRepository($request);
    }
    public static function deliveryRequest(Request $request)
    {
        return new DeliveryRepository($request);
    }
    public static function dashboardRequest(Request $request)
    {
        return new DashboardRepository($request);
    }
    public static function aiRequest(Request $request)
    {
        $file_service = new LocalFileService();
        $data_extractor = new PdfDataExtractorService();
        $data_restructure = new DataRestructureService();
        return new AiRepository($file_service,  $data_extractor, $data_restructure, $request);
    }
}
