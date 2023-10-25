<?php

namespace App\Repositories\Business;

use App\Repositories\Abstracts\RepositoryAbs;
use App\Models\Business\RawSoHeader;
use App\Utilities\UniqueIdUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RawSoHeaderRepository extends RepositoryAbs
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getRawSoHeaders()
    {
        $query = RawSoHeader::query();
        $query->orderBy('created_at', 'desc');
        $raw_so_headers = $query->get();
        return $raw_so_headers;
    }

    public function createRawSoHeader()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_id' => 'required|exists:customers,id',
                'raw_extract_header_id' => 'required|exists:raw_extract_headers,id',
                'uploaded_file_id' => 'required|exists:uploaded_files,id',
                'po_number' => 'string',
                'po_date' => 'datetime',
                'po_person' => 'string',
                'po_phone' => 'string',
                'po_delivery_date' => 'datetime',
                'po_delivery_address' => 'string',
                'po_note' => 'string',
                'note' => 'string',
            ], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function duplicateRawSoHeader()
    {
        try {
            $validator = Validator::make($this->data, [
                'raw_so_header' => 'required|exists:raw_so_headers,id'
            ], [
                'raw_so_header.required' => 'Raw SO Header là bắt bược',
                'raw_so_header.exists' => 'Raw SO Header không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $orginal_raw_so_header = RawSoHeader::query()->with('customer')->find($this->data['raw_so_header']);
                $new_raw_so_header = $orginal_raw_so_header->replicate();
                $new_raw_so_header->serial_number = UniqueIdUtility::generateSerialUniqueNumber($orginal_raw_so_header->customer->code);
                $new_raw_so_header->save();

                return $new_raw_so_header;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
