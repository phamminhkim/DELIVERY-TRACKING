<?php

namespace App\Repositories\Business;

use App\Repositories\Abstracts\RepositoryAbs;
use App\Models\Business\RawSoHeader;
use App\Models\Business\RawSoItem;
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

    public function getRawSoHeaderById($id)
    {
        $query = RawSoHeader::query();
        $query->with([
            'raw_so_items',
            'raw_so_items.raw_extract_item.customer_material',
            'raw_so_items.sap_material.unit',
            'customer.group',

        ]);

        $raw_so_header = $query->find($id);
        return $raw_so_header;
    }

    public function createPromotiveRawSoHeader()
    {
        try {
            $validator = Validator::make($this->data, [
                'raw_so_header' => 'required|exists:raw_so_headers,id',
                'items' => 'required|array',
                'items.*.sap_material' => 'required|exists:raw_extract_items,id',
                'items.*.quantity' => 'required|numeric|min:1',
            ], [
                'raw_so_header.required' => 'Raw SO Header là bắt bược',
                'raw_so_header.exists' => 'Raw SO Header không tồn tại',
                'items.required' => 'Sản phẩm là bắt buộc',
                'items.array' => 'Sản phẩm phải là một mảng',
                'items.*.sap_material.required' => 'Sản phẩm là bắt buộc',
                'items.*.sap_material.exists' => 'Sản phẩm không tồn tại',
                'items.*.quantity.required' => 'Số lượng là bắt buộc',
                'items.*.quantity.numeric' => 'Số lượng phải là số',
                'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $orginal_raw_so_header = RawSoHeader::query()->with('customer')->find($this->data['raw_so_header']);
                $new_raw_so_header = $orginal_raw_so_header->replicate();
                $new_raw_so_header->serial_number = UniqueIdUtility::generateSerialUniqueNumber($orginal_raw_so_header->customer->code);
                $new_raw_so_header->is_promotive = true;
                $new_raw_so_header->save();

                $items = $this->data['items'];
                foreach ($items as $item) {
                    $raw_so_item = RawSoItem::create([
                        'raw_extract_item_id' => null,
                        'raw_so_header_id' => $new_raw_so_header->id,
                        'sap_material_id' => $item['sap_material'],
                        'quantity' => $item['quantity'],
                        'percentage' => 100,
                        'is_promotive' => true,
                    ]);
                }

                $new_raw_so_header->load('raw_so_items');
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
        $raw_so_header = RawSoHeader::query()->find($id);
        if ($raw_so_header) {
            $raw_so_header->delete();
            RawSoItem::query()->where('raw_so_header_id', $id)->delete();
            return true;
        }
        return false;
    }

    public function updateRawSoHeader($id)
    {
        try {
            $raw_so_header = RawSoHeader::query()->find($id);
            if (!$raw_so_header) {
                $this->errors[] = 'Raw SO Header không tồn tại';
                return false;
            }
            $raw_so_header->update($this->data);
            return $raw_so_header;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
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
                'sap_material_id' => 'required|exists:raw_extract_items,id',
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
                return $raw_so_item;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateRawSoItem($raw_so_item_id)
    {
        try {
            $validator = Validator::make($this->data, [
                'quantity' => 'required|numeric|min:1',
            ], [
                'quantity.required' => 'Số lượng là bắt buộc',
                'quantity.numeric' => 'Số lượng phải là số',
                'quantity.min' => 'Số lượng phải lớn hơn 0',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $raw_so_item = RawSoItem::query()->find($raw_so_item_id);
                if (!$raw_so_item) {
                    $this->errors[] = 'Raw SO Item không tồn tại';
                    return false;
                }
                $raw_so_item->update($this->data);
                DB::commit();
                return $raw_so_item;
            }
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
