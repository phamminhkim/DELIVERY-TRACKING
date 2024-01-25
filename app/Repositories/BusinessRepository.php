<?php

namespace App\Repositories;


use App\Enums\Ai\Convert\ConvertMethod;
use App\Enums\Ai\Extract\ExtractMethod;
use App\Enums\Ai\Restructure\RestructureMethod;
use App\Repositories\Business\AiConfigRepository;
use App\Repositories\Business\AiRepository;
use App\Repositories\Business\ApplicationRepository;
use App\Repositories\Business\DashboardRepository;
use App\Repositories\Business\OrderRepository;
use App\Repositories\Business\DeliveryRepository;
use App\Repositories\Business\RawSoHeaderRepository;
use App\Repositories\Business\UploadedFileRepository;
use App\Services\DataRestructureService;
use App\Services\Implementations\Converters\LeagueCsvConverter;
use App\Services\Implementations\Converters\ManualConverter;
use App\Services\Implementations\Converters\RegexMatchConverter;
use App\Services\Implementations\Converters\RegexSplitConverter;
use App\Services\Implementations\Converters\TableConverterService;
use App\Services\Implementations\Extractors\CamelotExtractorService;
use App\Services\Implementations\Files\LocalFileService;
use App\Services\Implementations\Restructurers\IndexArrayMappingRestructure;
use App\Services\Implementations\Restructurers\KeyArrayMappingRestructure;
use App\Services\Implementations\Restructurers\MergeIndexArrayMappingRestructure;
use App\Services\Implementations\Restructurers\SearchTextArrayMappingRestructure;
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
        if ($request->filled('extract_method')) {
            $method = $request->extract_method; // Có thể là regex, camelot
            switch ($method) {
                case ExtractMethod::CAMELOT:
                    $data_extractor = new CamelotExtractorService();
                    break;
                default:
                    throw new \Exception('Extract method is not specified');
            }
        } else {
            // throw new \Exception('Data extractor not found');
            $data_extractor = new CamelotExtractorService();
        }
        if ($request->filled('convert_method')) {
            $method = $request->convert_method; // Có thể là regex, leaguecsv
            switch ($method) {
                case ConvertMethod::REGEXMATCH:
                    $table_converter = new RegexMatchConverter();
                    break;
                case ConvertMethod::REGEXSPLIT:
                    $table_converter = new RegexSplitConverter();
                    break;
                case ConvertMethod::LEAGUECSV:
                    $table_converter = new LeagueCsvConverter();
                    break;
                case ConvertMethod::MANUAL:
                    $table_converter = new ManualConverter();
                    break;
                default:
                    throw new \Exception('Convert method is not specified');
            }
        } else {
            // throw new \Exception('Convert method is not specified');
            $table_converter = new RegexMatchConverter();
        }
        if ($request->filled('restructure_method')) {
            $method = $request->restructure_method; // Có thể là regex, leaguecsv
            switch ($method) {
                case RestructureMethod::INDEXARRAYMAPPING:
                    $data_restructure = new IndexArrayMappingRestructure();
                    break;
                case RestructureMethod::KEYARRAYMAPPING:
                    $data_restructure = new KeyArrayMappingRestructure();
                    break;
                case RestructureMethod::MERGEINDEXARRAYMAPPING:
                    $data_restructure = new MergeIndexArrayMappingRestructure();
                    break;
                case RestructureMethod::SEARCHTEXTARRAYMAPPING:
                    $data_restructure = new SearchTextArrayMappingRestructure();
                    break;
                default:
                    throw new \Exception('Restructure method is not specified');
            }
        } else {
            // throw new \Exception('Restructure method is not specified');
            $data_restructure = new IndexArrayMappingRestructure();
        }
        return new AiRepository($file_service,  $data_extractor, $table_converter, $data_restructure, $request);
    }

    static function uploadedFileRequest(Request $request)
    {
        return new UploadedFileRepository($request);
    }

    static function rawSoHeaderRequest(Request $request)
    {
        return new RawSoHeaderRepository($request);
    }

}
