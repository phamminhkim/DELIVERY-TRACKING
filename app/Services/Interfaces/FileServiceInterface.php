<?php

namespace App\Services\Interfaces;

interface FileServiceInterface
{
    public function saveTemporaryFile($file);
    public function deleteTemporaryFile($file_path);

    public function saveProtectedFile($file, $path, $batch_id);
}
