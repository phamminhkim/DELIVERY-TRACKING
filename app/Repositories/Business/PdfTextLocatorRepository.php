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
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $page_num = $this->request->input('page_num');
            $search_text = $this->request->input('search_text');
            $index = $this->request->input('index');
            $result = $this->pdf_text_locator_service->findTextPosition($file_path, $page_num, $search_text, $index);
            $this->file_service->deleteTemporaryFile($file_path);
            if (isset($result['error'])) {
                $this->errors = $result['error'];
                return null;
            } else {
                return $result;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    // Lấy text theo tọa độ
    public function getTextByCoords()
    {
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $page_num = $this->request->input('page_num');
            $coords = $this->request->input('coords');
            $result = $this->pdf_text_locator_service->getTextByCoords($file_path, $page_num, $coords);
            $this->file_service->deleteTemporaryFile($file_path);
            if (isset($result['error'])) {
                $this->errors = $result['error'];
                return null;
            } else {
                return $result;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    // Lấy full text
    public function getFullText()
    {
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $page_num = $this->request->input('page_num');
            $result = $this->pdf_text_locator_service->getFullText($file_path, $page_num);
            $this->file_service->deleteTemporaryFile($file_path);
            if (isset($result['error'])) {
                $this->errors = $result['error'];
                return null;
            } else {
                return $result;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    // Check string key a file
    public function checkStringKey()
    {
        $result = [];
        try {
            $file = $this->request->file('file');
            $file_path = $this->file_service->saveTemporaryFile($file);
            $page_num = $this->request->input('page_num');
            $string_key = $this->request->input('string_key');
            $result = $this->pdf_text_locator_service->checkStringKey($file_path, $page_num, $string_key);
            $this->file_service->deleteTemporaryFile($file_path);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
        return $result;
    }
    // Check string key with multiple files
    public function checkStringKeyMultiFiles()
    {
        $result = [];
        try {
            $files = $this->request->file('file');
            $exist_count = 0;
            $exist_files = [];
            foreach ($files as $file) {
                $file_name = $file->getClientOriginalName();
                $file_path = $this->file_service->saveTemporaryFile($file);
                $page_num = $this->request->input('page_num');
                $string_key = $this->request->input('string_key');
                $json_result = $this->pdf_text_locator_service->checkStringKey($file_path, $page_num, $string_key);
                $this->file_service->deleteTemporaryFile($file_path);
                if (isset($json_result['is_exist']) && $json_result['is_exist'] == True) {
                    $exist_files[] = $file_name;
                    $exist_count++;
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
