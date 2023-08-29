<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class LocalFileService implements Interfaces\FileServiceInterface
{
    public function saveTemporaryFile($file)
    {
        $file_name = $file->getClientOriginalName();

        Storage::disk('temp')->put($file_name, file_get_contents($file));

        $file_path = Storage::disk('temp')->path($file_name);
        return $file_path;
    }

    public function deleteTemporaryFile($file_path)
    {
        Storage::disk('temp')->delete($file_path);
    }
}
