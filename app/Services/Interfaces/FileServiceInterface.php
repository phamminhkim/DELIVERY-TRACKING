<?php

namespace App\Services\Interfaces;

interface FileServiceInterface
{
    public function saveTemporaryFile($file);
    public function deleteTemporaryFile($file_path);
    public function deleteTemporaryFileByFilename($file_name);

    public function saveProtectedFile($file, $path, $batch_id);
    public function savePoFile($file);
}
