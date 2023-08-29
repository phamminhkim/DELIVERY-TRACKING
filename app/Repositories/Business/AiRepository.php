<?php

namespace App\Repositories\Business;

use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Interfaces\DataExtractorInterface;
use App\Services\Interfaces\DataRestructureInterface;
use App\Services\Interfaces\FileServiceInterface;
use App\Services\Interfaces\PdfExtractorInterface;
use Illuminate\Support\Facades\Storage;
use RandomState\Camelot\Camelot;
use League\Csv\Reader;

class AiRepository extends RepositoryAbs
{
    protected $file_service;
    protected $data_extractor;
    protected $data_restructure;

    public function __construct(
        FileServiceInterface $file_service,
        DataExtractorInterface $data_extractor,
        DataRestructureInterface $data_restructure,
        $request
    ) {
        parent::__construct($request);
        $this->file_service = $file_service;
        $this->data_extractor = $data_extractor;
        $this->data_restructure = $data_restructure;
    }
    public function extractOrder()
    {
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $raw_data = $this->extractData($file_path);
            $data = $this->restructureData($raw_data);

            $this->file_service->deleteTemporaryFile($file_path);
            return $data;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function extractData($file_path)
    {
        $table = $this->data_extractor->extractData($file_path, 'lattice');
        return $table;
    }

    private function restructureData($array)
    {
        $data = $array[0];

        $pattern = '/(\d+)\s+([\p{L}\p{N}]+)\s+(\d+)\s+(\d+)\s+([\d,\.]+)\s+([\d,\.]+)\s+([\d,\.]+)\s+(.+)/u';
        $collection = $this->data_restructure->withRegex($data, $pattern);
        if (count($collection) > 0) {
            return $collection;
        } else {
            $collection = $this->data_restructure->withLeagueCsv($data);
            return $collection;
        }
    }
}
