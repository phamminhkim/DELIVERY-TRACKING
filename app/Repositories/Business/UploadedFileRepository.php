<?php

namespace App\Repositories\Business;

use App\Enums\File\FileStatuses;
use App\Jobs\ExtractFile;
use App\Models\Business\Batch;
use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\FileStatus;
use App\Models\Business\UploadedFile;
use App\Models\Master\UserMorph;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Implementations\Files\LocalFileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadedFileRepository extends RepositoryAbs
{
    protected $file_service;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->file_service = new LocalFileService();
    }

    public function getFiles()
    {
        $user_id = $this->current_user->id;
        $query = UploadedFile::query();
        if ($this->current_user->hasRole('admin-system')) {
            // Không cần lọc theo user_id cho admin-system, hiển thị tất cả các tệp
        } else {
            // Lọc các tệp theo user_id của người dùng hiện tại
            $query->whereHas('user_morphs', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
        }


        // $query->whereHas('user_morphs', function ($query) use ($user_id) {
        //     $query->where('user_id', $user_id);
        // });
        if ($this->request->filled('customer_group_ids')) {
            $customer_group_ids = explode(',', $this->request->customer_group_ids);
            $query->whereHas('batch', function ($query) use ($customer_group_ids) {
                $query->whereHas('customer', function ($query) use ($customer_group_ids) {
                    $query->whereHas('group', function ($query) use ($customer_group_ids) {
                        $query->whereIn('customer_groups.id', $customer_group_ids);
                    });
                });
            });
        }

        if ($this->request->filled('customer_ids')) {
            $customer_ids = $this->request->customer_ids;
            $query->whereHas('batch', function ($query) use ($customer_ids) {
                $query->whereIn('batches.customer_id', $customer_ids);
            });
        }
        // if ($this->request->filled('status_ids')) {
        //     $statusIds = $this->request->status_ids;
        //     $query->whereHas('status', function ($query) use ($statusIds) {
        //         $query->whereIn('status_id', $statusIds);
        //     });
        // }
        if ($this->request->filled('status_ids')) {
            $statusIds = $this->request->status_ids;
            $query->whereIn('status_id', $statusIds);
        }

        if ($this->request->filled('from_date')) {
            $from_date = $this->request->from_date;
            $query->whereDate('created_at', '>=', $from_date);
        }

        if ($this->request->filled('to_date')) {
            $to_date = $this->request->to_date;
            $query->whereDate('created_at', '<=', $to_date);
        }
        $query
            ->with(['batch.customer.group', 'raw_so_headers', 'status', 'file_extract_error.extract_error', 'file_extract_error.log'])
            ->orderBy('created_at', 'desc');
        $files = $query->get();

        return $files;
    }
    public function getFilesById($id)
    {

        $user_id = $this->current_user->id;
        $query = UploadedFile::query();

        $query->whereHas('user_morphs', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });

        $query
            ->with(['batch.customer.group', 'raw_so_headers.raw_so_items', 'raw_so_headers.raw_so_items.sap_material', 'raw_so_headers.raw_so_items.raw_extract_item.customer_material', 'status', 'file_extract_error.extract_error', 'file_extract_error.log'])
            ->orderBy('created_at', 'desc');

        $file = $query->find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $excelData = $file->raw_so_headers->flatMap(function ($rawSoHeader) {
            return $rawSoHeader->raw_so_items->map(function ($item) use ($rawSoHeader) {
                return [
                    'Số SO' => $rawSoHeader->customer->name,
                    'Mã Khách hàng' => $rawSoHeader->customer->code,
                    'Mã sản phẩm' => $item->sap_material->sap_code,
                    'Số lượng' => $item->quantity,
                    'Đơn vị tính' => $item->sap_material->unit->unit_code,
                ];
            });
        });


        return $excelData;
    }
    public function prepareUploadFile()
    {
        try {
            $validator = Validator::make($this->data, [
                'extract_order_config' => 'required|exists:extract_order_configs,id',
                'customer' => 'required|exists:customers,id',
                'company' => 'required|exists:companies,code',
            ], [
                'extract_order_config.required' => 'Extract order config id là bắt buộc',
                'extract_order_config.exists' => 'Extract order config id không tồn tại',
                'customer.required' => 'Customer id là bắt buộc',
                'customer.exists' => 'Customer id không tồn tại',
                'company.required' => 'Company code là bắt buộc',
                'company.exists' => 'Company code không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $original_extract_order_config = ExtractOrderConfig::query()->with(['extract_data_config', 'convert_table_config', 'restructure_data_config'])->find($this->data["extract_order_config"]);
                if (!$original_extract_order_config) {
                    $this->message = 'Extract order config không tồn tại';
                    return;
                }
                $extract_order_config = $original_extract_order_config->replicate()->fill(['name' => 'waiting_to_add']);

                $extract_data_config = $original_extract_order_config->extract_data_config->replicate();
                $convert_table_config = $original_extract_order_config->convert_table_config->replicate();
                $restructure_data_config = $original_extract_order_config->restructure_data_config->replicate();
                $extract_data_config->save();
                $convert_table_config->save();
                $restructure_data_config->save();
                $extract_order_config->save();
                $batch = Batch::create([
                    'extract_order_config_id' => $extract_order_config->id,
                    'customer_id' => $this->data['customer'],
                    'company_code' => $this->data['company'],
                ]);

                $extract_order_config->fill([
                    'name' => $original_extract_order_config->name . ' - ' . $batch->id,
                    'extract_data_config_id' => $extract_data_config->id,
                    'convert_table_config_id' => $convert_table_config->id,
                    'restructure_data_config_id' => $restructure_data_config->id,
                    'reference_id' => $original_extract_order_config->id,
                    'is_official' => false
                ])->save();

                DB::commit();
                return $batch->id;
            }
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function uploadFile()
    {
        try {
            $validator = Validator::make($this->data, [
                'file' => 'required|file',
                'batch_id' => 'required|uuid|exists:batches,id',
            ], [
                'file.required' => 'File là bắt buộc',
                'file.file' => 'File không đúng định dạng',
                'batch_id.required' => 'Batch id là bắt buộc',
                'batch_id.uuid' => 'Batch id không đúng định dạng uuid',
                'batch_id.exists' => 'Batch id không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $new_status = FileStatus::where('code', FileStatuses::NEW)->first();
                DB::beginTransaction();
                $file = $this->request->file('file');
                $file_path = $this->file_service->saveProtectedFile($file, $this->current_user->id, $this->data['batch_id']);
                $uploaded_file = UploadedFile::create([
                    'path' => $file_path,
                    'batch_id' => $this->data['batch_id'],
                    'status_id' => $new_status->id,
                ]);
                $user_morph = new UserMorph(['user_id' => $this->current_user->id]);
                $uploaded_file->user_morphs()->save($user_morph);
                DB::commit();
                ExtractFile::dispatch($uploaded_file->id);
                return $uploaded_file;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function downloadFile($id)
    {
        $file = UploadedFile::query()->find($id);
        if (!$file) {
            $this->message = 'File không tồn tại';
            return;
        }
        // dd($file->users->pluck('morphable_id')->toArray(), $this->current_user->id);
        if (!(in_array($this->current_user->id, $file->user_morphs->pluck('user_id')->toArray()) || $this->current_user->hasRole('admin'))) {
            $this->message = 'Bạn không có quyền truy cập file này';
            return;
        }
        $file_path = $file->path;
        return Storage::disk('protected')->download($file_path);
    }

    public function deleteFile($id)
    {
        $file = UploadedFile::query()
            ->with('raw_so_headers')
            ->find($id);
        if (!$file) {
            $this->message = 'File không tồn tại';
            return;
        }
        if (!(in_array($this->current_user->id, $file->user_morphs->pluck('user_id')->toArray()) || $this->current_user->hasRole('admin'))) {
            $this->message = 'Bạn không có quyền truy cập file này';
            return;
        }

        $raw_so_header_repository = new RawSoHeaderRepository($this->request);
        foreach ($file->raw_so_headers as $raw_so_header) {
            $raw_so_header_repository->deleteRawSoHeader($raw_so_header->id);
        }
        $file->delete();
        return true;
    }
}
