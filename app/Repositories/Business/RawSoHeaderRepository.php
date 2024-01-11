<?php

namespace App\Repositories\Business;

use App\Enums\File\FileStatuses;
use App\Models\Business\FileStatus;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Models\Business\RawSoHeader;
use App\Models\Business\RawSoItem;
use App\Models\Business\UploadedFile;
use App\Utilities\UniqueIdUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RawSoHeaderRepository extends RepositoryAbs
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getRawSoHeaders($is_push_sap = false)
    {
        $query = RawSoHeader::query();

        if ($is_push_sap) {
            $query->where('is_wating_sync', true)
                ->with([
                    'raw_so_items',
                    'raw_so_items.sap_material.unit',
                    'customer',
                ]);
        }

        $query->orderBy('created_at', 'desc');
        $raw_so_headers = $query->get();
        return $raw_so_headers;
    }

    public function getRawSoHeaderById($id)
    {
        $query = RawSoHeader::query();
        $query->with([
            'raw_so_items',
            'raw_so_items.raw_extract_item.customer_material',
            'raw_so_items.sap_material.unit',
            'raw_so_items.warehouse',
            'customer.group',

        ]);

        $raw_so_header = $query->find($id);
        return $raw_so_header;
    }

    public function syncRawSoHeaderFromSap()
    {
        try {
            $validator = Validator::make($this->data, [
                'synced_so_headers' => 'required|array',
                'synced_so_headers.*.sap_so_number' => 'required',
                'synced_so_headers.*.serial_number' => 'required|exists:synced_so_headers,serial_number',
            ], [
                'synced_so_headers.required' => 'Raw SO Headers là bắt buộc',
                'synced_so_headers.array' => 'Raw SO Headers phải là mảng',
                'synced_so_headers.*.sap_so_number.required' => 'Số SO SAP là bắt buộc',
                'synced_so_headers.*.serial_number.required' => 'Serial Number là bắt buộc',
                'synced_so_headers.*.serial_number.exists' => 'Serial Number không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $sap_sos = collect($this->data['synced_so_headers']);
                $sap_so_maps = [];
                foreach ($sap_sos as $sap_so) {
                    $sap_so_maps[$sap_so['serial_number']] = $sap_so['sap_so_number'];
                }
                $serial_numbers = $sap_sos->pluck('serial_number')->toArray();
                $synced_so_headers = RawSoHeader::query()->whereIn('serial_number', $serial_numbers)->get();
                foreach ($synced_so_headers as $synced_so_header) {
                    $synced_so_header->sap_so_number = $sap_so_maps[$synced_so_header->serial_number];
                    $synced_so_header->is_wating_sync = false;
                    $synced_so_header->save();
                }

                $files = UploadedFile::query()->whereHas('raw_so_headers', function ($query) use ($serial_numbers) {
                    $query->whereIn('serial_number', $serial_numbers);
                })->with(['raw_so_headers'])->get();

                $success_status = FileStatus::query()->where('code', FileStatuses::SUCCESS)->first();
                foreach ($files as $file) {
                    $is_sync_all = true;
                    foreach ($file->raw_so_headers as $raw_so_header) {
                        if (!$raw_so_header->sap_so_number) {
                            $is_sync_all = false;
                            break;
                        }
                    }
                    if ($is_sync_all) {
                        $file->status_id = $success_status->id;
                        $file->save();
                    }
                }
                DB::commit();
                return $synced_so_headers;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function makeRawSoHeadersWatingToSync()
    {
        try {
            $validator = Validator::make($this->data, [
                'wating_sync_so_headers' => 'array',
                'wating_sync_so_headers.*' => 'exists:raw_so_headers,id',
                'waiting_sync_files' => 'array',
                'waiting_sync_files.*' => 'exists:uploaded_files,id',
            ], [
                'wating_sync_so_headers.array' => 'Raw SO Headers phải là mảng',
                'wating_sync_so_headers.*.exists' => 'Raw SO Header không tồn tại',
                'waiting_sync_files.array' => 'Files phải là mảng',
                'waiting_sync_files.*.exists' => 'File không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $waiting_to_sync_so_header_from_files = RawSoHeader::query()->whereIn('uploaded_file_id', $this->data['waiting_sync_files'] ?? [])->get();
                $wating_to_sync_so_headers = RawSoHeader::whereIn('id', $this->data['wating_sync_so_headers'] ?? [])->get();
                $wating_to_sync_so_headers = $wating_to_sync_so_headers->merge($waiting_to_sync_so_header_from_files);
                foreach ($wating_to_sync_so_headers as $wating_to_sync_so_header) {
                    $wating_to_sync_so_header->is_wating_sync = true;
                    $wating_to_sync_so_header->save();
                }
                $files = UploadedFile::query()->whereHas('raw_so_headers', function ($query) use ($wating_to_sync_so_headers) {
                    $query->whereIn('id', $wating_to_sync_so_headers->pluck('id')->toArray());
                })->get();

                $processing_status = FileStatus::query()->where('code', FileStatuses::WAITING_SYNC)->first();
                foreach ($files as $file) {
                    $file->status_id = $processing_status->id;
                    $file->save();
                }

                DB::commit();
                return $wating_to_sync_so_headers;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createPromotiveRawSoHeader()
    {
        try {
            $validator = Validator::make($this->data, [
                'raw_so_header' => 'required|exists:raw_so_headers,id',
            ], [
                'raw_so_header.required' => 'Raw SO Header là bắt bược',
                'raw_so_header.exists' => 'Raw SO Header không tồn tại',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $orginal_raw_so_header = RawSoHeader::query()->with('customer')->find($this->data['raw_so_header']);
                if ($orginal_raw_so_header->is_promotive) {
                    $this->errors[] = 'Đơn hàng khuyến mãi không thể tạo đơn hàng khuyến mãi';
                    return false;
                }
                $new_raw_so_header = $orginal_raw_so_header->replicate();
                $new_raw_so_header->sap_so_number = null;
                $new_raw_so_header->serial_number = UniqueIdUtility::generateSerialUniqueNumber($orginal_raw_so_header->customer->code);
                $new_raw_so_header->is_promotive = true;
                $new_raw_so_header->save();

                // $items = $this->data['items'];
                // foreach ($items as $item) {
                //     $raw_so_item = RawSoItem::create([
                //         'raw_extract_item_id' => null,
                //         'raw_so_header_id' => $new_raw_so_header->id,
                //         'sap_material_id' => $item['sap_material'],
                //         'quantity' => $item['quantity'],
                //         'percentage' => 100,
                //         'is_promotive' => true,
                //     ]);
                // }

                // $new_raw_so_header->load('raw_so_items');
                DB::commit();
                return $new_raw_so_header;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function deleteRawSoHeader($id)
    {
        $raw_so_header = RawSoHeader::query()
            ->with('raw_so_items', 'raw_extract_header.raw_extract_items')
            ->find($id);
        if (!$raw_so_header) {
            return false;
        }
        if ($raw_so_header->raw_extract_header) {
            $raw_so_header->raw_extract_header->raw_extract_items()->delete();
            $raw_so_header->raw_extract_header->delete();
        }
        $raw_so_header->raw_so_items()->delete();
        $raw_so_header->delete();
        return true;
    }

    public function updateRawSoHeader($id)
    {
        try {
            DB::beginTransaction();
            $raw_so_header = RawSoHeader::query()->findOrFail($id);
            if (!$raw_so_header) {
                $this->errors[] = 'Raw SO Header không tồn tại';
                return false;
            }
            $raw_so_header->update($this->data);
            $raw_so_items = $this->data['raw_so_items'];
            $updated_warehouse_items = []; // Mảng để lưu trữ các mục hàng của kho đã được cập nhật
            $warehouses = []; // Mảng để lưu trữ warehouse_ids đã được xử lý
            foreach ($raw_so_items as $raw_so_item) {
                if (isset($raw_so_item['id']) && $raw_so_item['id'] > 0) {
                    $item = RawSoItem::query()->find($raw_so_item['id']);
                    if ($item) {
                        $item->fill($raw_so_item);
                        // Kiểm tra xem có sự thay đổi trong warehouse_id không
                        if ($item->warehouse_id != $raw_so_item['warehouse_id']) {
                            $updated_warehouse_items[] = $item; // Lưu trữ các mục hàng của kho đã được cập nhật
                        }

                        $item->save();
                    }
                } else {
                    $item = new RawSoItem;
                    $item->raw_so_header_id = $raw_so_header->id;
                    $item->fill($raw_so_item);
                    $item->save();
                }

                // Tách đơn nếu warehouse_id chưa được xử lý
                if (!in_array($raw_so_item['warehouse_id'], $warehouses)) {
                    $warehouses[] = $raw_so_item['warehouse_id'];
                }
            }
            $raw_so_items_deleted = $this->data['raw_so_items_deleted'];
            foreach ($raw_so_items_deleted as $raw_so_item_deleted) {
                if (isset($raw_so_item_deleted['id']) && $raw_so_item_deleted['id'] > 0) {
                    $item = RawSoItem::query()->find($raw_so_item_deleted['id']);
                    if ($item) {
                        $item->delete();
                    }
                }
            }
            // Tách đơn cho các mục hàng của kho đã được cập nhật
            foreach ($updated_warehouse_items as $item) {
                $this->createSeparateOrder($raw_so_header, $item->warehouse_id, $item);
            }

            // Tách đơn cho các mục hàng của kho mới
            foreach ($warehouses as $warehouse_id) {
                $this->createSeparateOrder($raw_so_header, $warehouse_id);
            }
            $raw_so_header->delete();

            DB::commit();
            return $raw_so_header;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    private function createSeparateOrder($raw_so_header, $warehouse_id, $item = null)
    {
        $separate_order = new RawSoHeader;
        $separate_order->fill($raw_so_header->toArray());

        // Tạo một số serial_number duy nhất
        $new_serial_number = uniqid(); // Sử dụng hàm uniqid() để tạo một giá trị ngẫu nhiên duy nhất

        $separate_order->serial_number = $new_serial_number;
        $separate_order->save();

        $items = $raw_so_header->raw_so_items()->where('warehouse_id', $warehouse_id)->get();
        foreach ($items as $item) {
            $newItem = new RawSoItem;
            $newItem->fill($item->toArray());
            $newItem->raw_so_header_id = $separate_order->id;
            $newItem->save();
        }
    }
    public function deleteRawSoItem($raw_so_item_id)
    {
        $raw_so_item = RawSoItem::query()->find($raw_so_item_id);
        if ($raw_so_item) {
            $raw_so_item->delete();
            return true;
        }
        return false;
    }

    public function addRawSoItemToRawSoHeader()
    {
        try {
            $validator = Validator::make($this->data, [
                'raw_so_header_id' => 'required|exists:raw_so_headers,id',
                'sap_material_id' => 'required|exists:sap_materials,id',
                'quantity' => 'required|numeric|min:1',
                'is_promotive' => 'required|boolean',
            ], [
                'raw_so_header_id.required' => 'Raw SO Header là bắt bược',
                'raw_so_header_id.exists' => 'Raw SO Header không tồn tại',
                'sap_material_id.required' => 'Sản phẩm là bắt buộc',
                'sap_material_id.exists' => 'Sản phẩm không tồn tại',
                'quantity.required' => 'Số lượng là bắt buộc',
                'quantity.numeric' => 'Số lượng phải là số',
                'quantity.min' => 'Số lượng phải lớn hơn 0',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $raw_so_item = RawSoItem::create([
                    'raw_extract_item_id' => null,
                    'raw_so_header_id' => $this->data['raw_so_header_id'],
                    'sap_material_id' => $this->data['sap_material_id'],
                    'quantity' => $this->data['quantity'],
                    'percentage' => 100,
                    'is_promotive' => $this->data['is_promotive'],
                ]);
                DB::commit();
                $raw_so_item->load('sap_material.unit');
                return $raw_so_item;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    // public function updateRawSoItem($raw_so_item_id)
    // {
    //     try {
    //         $validator = Validator::make($this->data, [
    //             'quantity' => 'required|numeric|min:1',
    //         ], [
    //             'quantity.required' => 'Số lượng là bắt buộc',
    //             'quantity.numeric' => 'Số lượng phải là số',
    //             'quantity.min' => 'Số lượng phải lớn hơn 0',
    //         ]);
    //         if ($validator->fails()) {
    //             $this->errors = $validator->errors()->all();
    //         } else {
    //             DB::beginTransaction();
    //             $raw_so_item = RawSoItem::query()->find($raw_so_item_id);
    //             if (!$raw_so_item) {
    //                 $this->errors[] = 'Raw SO Item không tồn tại';
    //                 return false;
    //             }
    //             $raw_so_item->update($this->data);
    //             DB::commit();
    //             return $raw_so_item;
    //         }
    //     } catch (\Throwable $exception) {
    //         DB::rollBack();
    //         $this->message = $exception->getMessage();
    //         $this->errors = $exception->getTrace();
    //     }
    // }
}
