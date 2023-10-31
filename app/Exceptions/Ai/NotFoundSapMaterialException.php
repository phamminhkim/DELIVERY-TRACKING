<?php

namespace App\Exceptions\Ai;

use App\Enums\Ai\Error\ExtractErrors;
use App\Models\Business\ExtractError;
use App\Models\Business\FileExtractError;
use App\Models\Business\FileExtractErrorLog;
use Exception;
use App\Exceptions\Ai\AbstractCustomException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotFoundSapMaterialException extends AbstractCustomException
{
    protected $uploaded_file_id;
    protected $error_log;

    public function __construct($uploaded_file_id, $error_log)
    {
        $this->uploaded_file_id = $uploaded_file_id;
        $this->error_log = $error_log;
        parent::__construct($error_log);
    }

    public function report()
    {
        DB::rollBack();
        Log::info('NotFoundSapMaterialException');
        $not_found_sap_error = ExtractError::query()->where('code', ExtractErrors::NOT_FOUND_SAP_MATERIAL)->first();
        $file_extract_error = FileExtractError::create([
            'uploaded_file_id' => $this->uploaded_file_id,
            'extract_error_id' => $not_found_sap_error->id,
        ]);
        FileExtractErrorLog::create([
            'file_extract_error_id' => $file_extract_error->id,
            'log' => $this->error_log,
        ]);
    }
}
