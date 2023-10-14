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

    public function saveProtectedFile($file, $user_id, $batch_id = null)
    {
        if (!Storage::disk('protected')->exists($user_id)) {
            Storage::disk('protected')->makeDirectory($user_id);
        }
        $file_name = $file->getClientOriginalName();
        $file_name = str_replace(' ', '_', $file_name);
        $spit_file_name = explode('.', $file_name);
        $file_name = $spit_file_name[0] . '_' . $batch_id . '.' . $spit_file_name[1];
        Storage::disk('protected')->put($user_id . '/' . $file_name, file_get_contents($file));

        return $user_id . '/' . $file_name;
    }
}
