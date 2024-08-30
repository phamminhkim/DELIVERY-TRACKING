<?php

namespace App\Repositories\Business;

use App\Services\Implementations\Extractors\PdfTextLocatorService;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Interfaces\FileServiceInterface;
use Exception;
use Illuminate\Http\Request;

class PdfTextLocatorRepository extends RepositoryAbs
{
    protected $file_service;
    protected $pdf_text_locator_service;
    public function __construct(
        FileServiceInterface $file_service,
        $request)
    {
        parent::__construct($request);
        $this->file_service = $file_service;
        $this->pdf_text_locator_service = new PdfTextLocatorService();
    }
    // Tìm tọa độ text
    public function findTextPosition()
    {
        $file_service = $this->file_service;
        $request = $this->request;
        $pdf_text_locator_service = $this->pdf_text_locator_service;
        $file_path = null;
        $result = null;

        try {
            $file = $request->file('file');
            $file_path = $file_service->saveTemporaryFile($file);
            $page_num = $request->input('page_num');
            $search_text = $request->input('search_text');
            $index = $request->input('index');
            $result = $pdf_text_locator_service->findTextPosition($file_path, $page_num, $search_text, $index);

            if (isset($result['error'])) {
                $this->errors = $result['error'];
                $result = null;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            $result = null;
        } finally {
            if ($file_path) {
                $file_service->deleteTemporaryFile($file_path);
            }
        }

        return $result;
    }
    // Lấy text theo tọa độ
    public function getTextByCoords()
    {
        $file_service = $this->file_service;
        $request = $this->request;
        $pdf_text_locator_service = $this->pdf_text_locator_service;
        $file_path = null;
        $result = null;

        try {
            $file = $request->file('file');
            $file_path = $file_service->saveTemporaryFile($file);
            $page_num = $request->input('page_num');
            $coords = $request->input('coords');
            $result = $pdf_text_locator_service->getTextByCoords($file_path, $page_num, $coords);

            if (isset($result['error'])) {
                $this->errors = $result['error'];
                $result = null;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            $result = null;
        } finally {
            if ($file_path) {
                $file_service->deleteTemporaryFile($file_path);
            }
        }

        return $result;
    }
    // Lấy full text
    public function getFullText()
    {
        $file_service = $this->file_service;
        $request = $this->request;
        $pdf_text_locator_service = $this->pdf_text_locator_service;
        $file_path = null;
        $result = null;

        try {
            $file = $request->file('file');
            $file_path = $file_service->saveTemporaryFile($file);
            $page_num = $request->input('page_num');
            $result = $pdf_text_locator_service->getFullText($file_path, $page_num);

            if (isset($result['error'])) {
                $this->errors = $result['error'];
                $result = null;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            $result = null;
        } finally {
            if (isset($file_path)) {
                $file_service->deleteTemporaryFile($file_path);
            }
        }

        return $result ?? null;
    }
    // Check string key a file
    public function checkStringKey()
    {
        $file_service = $this->file_service;
        $request = $this->request;
        $pdf_text_locator_service = $this->pdf_text_locator_service;
        $file_path = null;
        $result = [];

        try {
            $file = $request->file('file');
            $file_path = $file_service->saveTemporaryFile($file);
            $page_num = $request->input('page_num');
            $string_key = $request->input('string_key');
            $result = $pdf_text_locator_service->checkStringKey($file_path, $page_num, $string_key);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        } finally {
            if (isset($file_path)) {
                $file_service->deleteTemporaryFile($file_path);
            }
        }

        return $result;
    }
    // Check string key with multiple files
    public function checkStringKeyMultiFiles()
    {
        $file_service = $this->file_service;
        $request = $this->request;
        $pdf_text_locator_service = $this->pdf_text_locator_service;
        $result = [];
        $exist_count = 0;
        $exist_files = [];

        try {
            $files = $request->file('file');
            $page_num = $request->input('page_num');
            $string_key = $request->input('string_key');

            foreach ($files as $file) {
                $file_name = $file->getClientOriginalName();
                $file_path = $file_service->saveTemporaryFile($file);

                try {
                    $json_result = $pdf_text_locator_service->checkStringKey($file_path, $page_num, $string_key);
                    if (isset($json_result['is_exist']) && $json_result['is_exist'] == true) {
                        $exist_files[] = $file_name;
                        $exist_count++;
                    }
                } finally {
                    $file_service->deleteTemporaryFile($file_path);
                }
            }

            $result['exist_files'] = $exist_files;
            $result['exist_count'] = $exist_count;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }

        return $result;
    }
}
