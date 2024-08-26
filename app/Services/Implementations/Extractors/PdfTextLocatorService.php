<?php

namespace App\Services\Implementations\Extractors;

use Illuminate\Support\Facades\Log;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class PdfTextLocatorService
{
    protected $python_script_path;

    public function __construct()
    {
        // Đường dẫn tới file Python script
        $this->python_script_path = base_path('app\Services\Implementations\Extractors\Python\find_text_position.py');
    }

    public function findTextPosition($pdf_path, $page_num, $search_text, $index)
    {
        $result = null;
        $method = "get_coords_by_text";
        $cmd = sprintf(
            'python %s --pdf_path %s --method %s --page_num %d --search_text %s --index %d',
            $this->python_script_path,
            escapeshellarg($pdf_path),
            $method,
            $page_num,
            escapeshellarg($search_text),
            $index
        );

        $output = [];
        $return_code = 0;
        exec($cmd, $output, $return_code);

        if ($return_code == 0) {
            $result['error'] = "Command failed with return code: " . $return_code;
        } else {
            $output_str = implode("\n", $output);
            $result = json_decode($output_str, true);
        }
        return $result;
    }
    public function getTextByCoords($pdf_path, $page_num, $coords)
    {
        $result = null;
        $method = "get_text_by_coords";
        $cmd = sprintf(
            'python %s --pdf_path %s --method %s --page_num %d --coords %s',
            $this->python_script_path,
            escapeshellarg($pdf_path),
            $method,
            $page_num,
            escapeshellarg($coords)
        );

        $output = [];
        $return_code = 0;
        exec($cmd, $output, $return_code);

        if ($return_code == 0) {
            $result['error'] = "Command failed with return code: " . $return_code;
        } else {
            $output_str = implode("\n", $output);
            $result = json_decode($output_str, true);
        }
        return $result;
    }
    public function getFullText($pdf_path, $page_num)
    {
        $result = null;
        $method = "get_full_text";
        $dir = (new TemporaryDirectory())->create();
        $output_path = $dir->path('extract.txt');

        $cmd = sprintf(
            'python %s --pdf_path %s --method %s --page_num %d --output_path %s',
            $this->python_script_path,
            escapeshellarg($pdf_path),
            $method,
            $page_num,
            escapeshellarg($output_path),
        );

        $output = [];
        $return_code = 0;
        exec($cmd, $output, $return_code);

        if ($return_code == 0) {
            $result['error'] = "Command failed with return code: " . $return_code;
        } else {
            $output_str = implode("\n", $output);
            $cmd_result = json_decode($output_str, true);
            if (isset($cmd_result['error'])) {
                $result = $cmd_result;
            } else {
                $result = $this->getFilesContents($output_path);
            }
        }
        $dir->delete();
        return $result;
    }
    public function checkStringKey($pdf_path, $page_num, $string_key)
    {
        $result = null;
        $method = "check_string_key";
        $cmd = sprintf(
            'python %s --pdf_path %s --method %s --page_num %d --string_key %s',
            $this->python_script_path,
            escapeshellarg($pdf_path),
            $method,
            $page_num,
            escapeshellarg($string_key)
        );

        $output = [];
        $return_code = 0;
        exec($cmd, $output, $return_code);

        if ($return_code == 0) {
            $result['error'] = "Command failed with return code: " . $return_code;
        } else {
            $output_str = implode("\n", $output);
            $result = json_decode($output_str, true);
        }
        return $result;
    }
    protected function getFilesContents($filePath)
    {
        $output = [];
        $output[] = $content = file_get_contents($filePath);

        return $output;
    }
}
