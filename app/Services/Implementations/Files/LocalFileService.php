<?php

namespace App\Services\Implementations\Files;

use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class LocalFileService implements FileServiceInterface
{
    public function saveTemporaryFile($file)
    {
        $full_file_name = $file->getClientOriginalName();
        $file_extension = pathinfo($full_file_name, PATHINFO_EXTENSION);
        $file_name_without_extension = pathinfo($full_file_name, PATHINFO_FILENAME);
        $file_name_without_extension = str_replace(' ', '_', $file_name_without_extension);
        // Generate a unique file name
        $unique_file_name = $file_name_without_extension . '_' . uniqid() . '.' . $file_extension;
        Storage::disk('temp')->put($unique_file_name, file_get_contents($file));

        $file_path = Storage::disk('temp')->path($unique_file_name);
        return $file_path;
    }

    public function deleteTemporaryFile($file_path)
    {
        // Storage::disk('temp')->delete($file_path);
        if (file_exists($file_path)) {
            unlink($file_path);
        }

    }
    public function deleteTemporaryFileByFilename($file_name)
    {
        Storage::disk('temp')->delete($file_name);
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

    public function savePoFile($file)
    {
        $date = Carbon::today()->format('Ymd');
        if (!Storage::disk('protected')->exists($date)) {
            Storage::disk('protected')->makeDirectory($date);
        }
        $full_file_name = $file->getClientOriginalName();
        $file_extension = pathinfo($full_file_name, PATHINFO_EXTENSION);
        $file_name_without_extension = pathinfo($full_file_name, PATHINFO_FILENAME);
        $file_name_without_extension = str_replace(' ', '_', $file_name_without_extension);
        // Generate a unique file name
        $unique_file_name = $file_name_without_extension . '_' . uniqid() . '.' . $file_extension;
        $file_path = $date . '/' . $unique_file_name;
        Storage::disk('protected')->put($file_path, file_get_contents($file));

        return $file_path;
    }
}
