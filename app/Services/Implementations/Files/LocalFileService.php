<?php

namespace App\Services\Implementations\Files;

use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Support\Carbon;
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
        $date = Carbon::today()->format('Ymd');
        if (!Storage::disk('protected')->exists($date)) {
            Storage::disk('protected')->makeDirectory($date);
        }
        $file_name = $file->getClientOriginalName();
        $file_name = str_replace(' ', '_', $file_name);
        $spit_file_name = explode('.', $file_name);
        $file_name = $spit_file_name[0] . '_' . $batch_id . '.' . $spit_file_name[1];
        Storage::disk('protected')->put($date . '/' . $file_name, file_get_contents($file));

        return $date . '/' . $file_name;
    }
}
