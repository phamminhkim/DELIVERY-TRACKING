<?php

namespace App\Services\Implementations\Files;

use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Support\Facades\Storage;

class LocalFileService implements FileServiceInterface
{
    public function saveTemporaryFile($file)
    {
        $file_name = $file->getClientOriginalName();
        $file_name = str_replace(' ', '_', $file_name);
        Storage::disk('temp')->put($file_name, file_get_contents($file));

        $file_path = Storage::disk('temp')->path($file_name);
        return $file_path;
    }

    public function deleteTemporaryFile($file_path)
    {
        Storage::disk('temp')->delete($file_path);
    }
}
