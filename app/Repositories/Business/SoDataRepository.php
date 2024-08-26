<?php

namespace App\Repositories\Business;

use App\Enums\File\FileStatuses;
use App\Models\Business\FileStatus;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Models\Business\SoHeader;
use App\Models\Business\SoDataItem;
use App\Models\Business\OrderProcess;
use App\Models\Business\UploadedFile;
use App\ThemeColor;
use App\Utilities\UniqueIdUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SoDataRepository extends RepositoryAbs
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function saveSoData()
    {
        try {
            $validator = Validator::make($this->data, [
                'title' => 'required',
                'order_data.*.sap_so_number' => 'required',
            ], [
                'title.required' => 'Title là bắt buộc',
                'order_data.*.sap_so_number.required' => 'Số SAP SO là bắt buộc'
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $current_user_id = $this->current_user->id;
                $serial_number = UniqueIdUtility::generateSerialUniqueNumber('Order');
                $order_process = OrderProcess::create([
                    'customer_group_id' => $this->data['customer_group_id'],
                    'serial_number' => $serial_number,
                    'title' => $this->data['title'],
                    'created_by' => $current_user_id,
                    'updated_by' => $current_user_id,
                ]);
                if ($order_process->id) {
                    $order_data = collect($this->data['order_data'])->groupBy(['sap_so_number', 'promotive_name'])->map(function ($order_items, $key) use ($order_process) {
                        $order_data_items = collect($order_items)->map(function ($so_items) use ($key, $order_process) {
                            $so_header = SoHeader::create([
                                'order_process_id' => $order_process->id,
                                'sap_so_number' => $key,
                                'po_number' => $so_items[0]['po_number'],
                                'so_sap_note' => isset($so_items[0]['so_sap_note']) ? $so_items[0]['so_sap_note'] : null,
                                'po_delivery_date' => isset($so_items[0]['po_delivery_date']) ? $so_items[0]['po_delivery_date'] : null,
                                'customer_name' => isset($so_items[0]['customer_name']) ? $so_items[0]['customer_name'] : null,
                                'customer_code' => isset($so_items[0]['customer_code']) ? $so_items[0]['customer_code'] : null,
                                'note' => isset($so_items[0]['note1']) ? $so_items[0]['note1'] : null,
                                'level2' => isset($so_items[0]['level2']) ? $so_items[0]['level2'] : null,
                                'level3' => isset($so_items[0]['level3']) ? $so_items[0]['level3'] : null,
                                'level4' => isset($so_items[0]['level4']) ? $so_items[0]['level4'] : null,
                            ]);
                            $so_number = UniqueIdUtility::generateSerialUniqueNumber($so_header->customer_code);
                            $so_data_items = collect($so_items)->map(function ($item) use ($so_header, $order_process, $so_number) {
                                $date_now = now();
                                return [
                                    'so_number' => $so_number,
                                    'order_process_id' => $order_process->id,
                                    'so_header_id' => $so_header->id,
                                    'order' => $item['order'],
                                    'barcode' => $item['barcode'],
                                    'sku_sap_code' => $item['sku_sap_code'],
                                    'sku_sap_name' => $item['sku_sap_name'],
                                    'sku_sap_unit' => $item['sku_sap_unit'],
                                    'is_promotive' => $item['is_promotive'],
                                    'promotive_name' => $item['promotive_name'],
                                    'note' => $item['note'],
                                    'customer_sku_code' => $item['customer_sku_code'],
                                    'customer_sku_name' => $item['customer_sku_name'],
                                    'customer_sku_unit' => $item['customer_sku_unit'],
                                    'quantity1_po' => $item['quantity1_po'],
                                    'quantity2_po' => $item['quantity2_po'],
                                    'quantity3_sap' => $item['quantity3_sap'],
                                    'is_inventory' => $item['is_inventory'],
                                    'inventory_quantity' => $item['inventory_quantity'],
                                    'price_po' =>  $item['price_po'] ? str_replace(",", "", $item['price_po']) : null,
                                    'amount_po' => $item['amount_po'] ? str_replace(",", "", $item['amount_po']) : null,
                                    'company_price' => $item['company_price'] ? str_replace(",", "", $item['company_price']) : null,
                                    'compliance' => $item['compliance'],
                                    'is_compliant' => $item['is_compliant'],
                                    'created_at' => $date_now,
                                    'updated_at' => $date_now,
                                ];
                            });
                            $insert_so_item = SoDataItem::insert($so_data_items->toArray());
                            if ($insert_so_item) {
                                // $so_data_items_id = SoDataItem::where('order_process_id', $order_process->id)->pluck('id')->toArray();
                                $so_data_items_id = SoDataItem::where('so_header_id', $so_header->id)->pluck('id')->toArray();

                                $theme_colors = collect($so_items)->map(function ($item) {
                                    if (!isset($item['theme_color'])) {
                                        $item['theme_color'] = [
                                            'background' => '#ffffff',
                                            'text' => '#000000',
                                        ];
                                    }
                                    $theme_color = $item['theme_color'];
                                    $obj_color = (object) [
                                        'background' => $theme_color['background'],
                                        'text' => $theme_color['text'],
                                    ];
                                    return [
                                        'background' => $obj_color->background,
                                        'text' => $obj_color->text,
                                    ];
                                });
                                // dd($theme_colors[1], $so_data_items_id);
                                foreach ($so_data_items_id as $key => $item_id) {
                                    // dd('key'.$key, 'item'.$item_id);
                                    $create_theme_color = ThemeColor::create([
                                        'so_data_item_id' => $item_id,
                                        'background' => $theme_colors[$key]['background'],
                                        'text' => $theme_colors[$key]['text'],
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            }
                        });
                    });
                }
                DB::commit();
                $order_process->load(['so_data_items', 'so_data_items.so_header']);
                return $order_process;
            }
            return false;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateSoData($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'title' => 'required',
                'order_data.*.sap_so_number' => 'required',
            ], [
                'title.required' => 'Title là bắt buộc',
                'order_data.*.sap_so_number.required' => 'Số SAP SO là bắt buộc'
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $current_user_id = $this->current_user->id;
                $order_process = OrderProcess::findOrFail($id);

                $order_proccess_data = $this->data['order_data'];
                $sync_so_data_items = array();
                // Loại bỏ những item đầu vào đã hoặc đang đồng bộ SAP
                foreach ($order_proccess_data as $key => $item) {
                    $is_sync = $order_process->is_sync_so_data($item['sap_so_number'], $item['promotive_name']);
                    if ($is_sync) {
                        // Lưu $order_proccess_data[$key] vào mảng sync_so_data_items
                        $sync_so_data_items[] = $item;
                        unset($order_proccess_data[$key]);
                    }
                }
                // Kiểm tra tất cả đơn đã hoặc đang đồng bộ SAP
                if (!$order_proccess_data) {
                    $this->errors["sync_all_data"] = "Tất cả đơn hàng đã hoặc đang được đồng bộ SAP";
                    return false;
                }

                // Xóa tất cả so_data_items và so_headers của order_process
                $order_process->not_sync_so_data_items->each(function ($so_data_item) {
                    if ($so_data_item->theme_color) {
                        $so_data_item->theme_color->delete();
                    }
                });
                $order_process->not_sync_so_data_items()->delete();
                $order_process->not_sync_so_headers()->delete();
                $order_process->updated_by = $current_user_id;
                $order_process->title = $this->data['title'];
                $order_process->customer_group_id = $this->data['customer_group_id'];
                $order_process->updated_at = now();
                $order_process->save();

                // Tạo lại so_data_items và so_headers
                $order_data = collect($order_proccess_data)->groupBy(['sap_so_number', 'promotive_name'])->map(function ($order_items, $key) use ($order_process) {
                    $order_data_items = collect($order_items)->map(function ($so_items) use ($key, $order_process) {
                        $so_header = SoHeader::create([
                            'order_process_id' => $order_process->id,
                            'sap_so_number' => $key,
                            'po_number' => $so_items[0]['po_number'],
                            'so_sap_note' => isset($so_items[0]['so_sap_note']) ? $so_items[0]['so_sap_note'] : null,
                            'po_delivery_date' => isset($so_items[0]['po_delivery_date']) ? $so_items[0]['po_delivery_date'] : null,
                            'customer_name' => isset($so_items[0]['customer_name']) ? $so_items[0]['customer_name'] : null,
                            'customer_code' => isset($so_items[0]['customer_code']) ? $so_items[0]['customer_code'] : null,
                            'note' => isset($so_items[0]['note1']) ? $so_items[0]['note1'] : null,
                            'level2' => isset($so_items[0]['level2']) ? $so_items[0]['level2'] : null,
                            'level3' => isset($so_items[0]['level3']) ? $so_items[0]['level3'] : null,
                            'level4' => isset($so_items[0]['level4']) ? $so_items[0]['level4'] : null,
                        ]);
                        $so_number = UniqueIdUtility::generateSerialUniqueNumber($so_header->customer_code);
                        $so_data_items = collect($so_items)->map(function ($item) use ($so_header, $order_process, $so_number) {
                            $date_now = now();
                            return [
                                'so_number' => $so_number,
                                'order_process_id' => $order_process->id,
                                'so_header_id' => $so_header->id,
                                'order' => $item['order'],
                                'barcode' => $item['barcode'],
                                'sku_sap_code' => $item['sku_sap_code'],
                                'sku_sap_name' => $item['sku_sap_name'],
                                'sku_sap_unit' => $item['sku_sap_unit'],
                                'is_promotive' => $item['is_promotive'],
                                'promotive_name' => $item['promotive_name'],
                                'note' => $item['note'],
                                'customer_sku_code' => $item['customer_sku_code'],
                                'customer_sku_name' => $item['customer_sku_name'],
                                'customer_sku_unit' => $item['customer_sku_unit'],
                                'quantity1_po' => $item['quantity1_po'],
                                'quantity2_po' => $item['quantity2_po'],
                                'quantity3_sap' => $item['quantity3_sap'],
                                'is_inventory' => $item['is_inventory'],
                                'inventory_quantity' => $item['inventory_quantity'],
                                'price_po' =>  $item['price_po'] ? str_replace(",", "", $item['price_po']) : null,
                                'amount_po' => $item['amount_po'] ? str_replace(",", "", $item['amount_po']) : null,
                                'company_price' => $item['company_price'] ? str_replace(",", "", $item['company_price']) : null,
                                'compliance' => $item['compliance'],
                                'is_compliant' => $item['is_compliant'],
                                'created_at' => $date_now,
                                'updated_at' => $date_now,
                            ];
                        });
                        $insert_so_item = SoDataItem::insert($so_data_items->toArray());
                        if ($insert_so_item) {
                            $so_data_items_id = SoDataItem::where('so_header_id', $so_header->id)->pluck('id')->toArray();
                            $theme_colors = collect($so_items)->map(function ($item) {
                                if (!isset($item['theme_color'])) {
                                    $item['theme_color'] = [
                                        'background' => '#ffffff',
                                        'text' => '#000000',
                                    ];
                                }
                                $theme_color = $item['theme_color'];
                                $obj_color = (object) [
                                    'background' => $theme_color['background'],
                                    'text' => $theme_color['text'],
                                ];
                                return [
                                    'background' => $obj_color->background,
                                    'text' => $obj_color->text,
                                ];
                            });
                            foreach ($so_data_items_id as $key => $item_id) {
                                $create_theme_color = ThemeColor::create([
                                    'so_data_item_id' => $item_id,
                                    'background' => $theme_colors[$key]['background'],
                                    'text' => $theme_colors[$key]['text'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    });
                });
                DB::commit();
                $so_data =  $order_process->so_headers();
                $not_sync_so_data =  $order_process->not_sync_so_headers();
                $sync_so_data =  $order_process->sync_so_headers();
                $this->message['so_count'] = $so_data->count();
                $this->message['not_sync_so_count'] = $not_sync_so_data->count();
                $this->message['sync_so_count'] = $sync_so_data->count();
                if (count($sync_so_data_items) > 0) {
                    $this->message['skip_save_items'] = collect($sync_so_data_items)->map(function ($item) {
                        return [
                            'order' => $item['order'],
                            'sap_so_number' => $item['sap_so_number'],
                            'promotive_name' => $item['promotive_name'],
                        ];
                    });
                    // Lấy các so  key không lưu
                    $skip_save_so_keys = collect($sync_so_data_items)->map(function ($item) {
                        return $item['sap_so_number'] . $item['promotive_name'];
                    })->toArray();
                    $this->message['skip_save_so_keys'] = array_values(array_unique($skip_save_so_keys));
                }
                $order_process->load(['so_data_items', 'so_data_items.so_header']);
                return $order_process;
            }
            return false;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getSoData($id)
    {
        try {
            $order_process = OrderProcess::findOrFail($id);
            $order_process->load(['so_data_items.theme_color', 'so_data_items.so_header']);
            return $order_process;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteSoData($id)
    {
        try {
            $validator = Validator::make($this->data, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $current_user_id = $this->current_user->id;
                DB::beginTransaction();
                $order_process = OrderProcess::findOrFail($id);
                $order_process->is_deleted = true;
                $order_process->updated_by = $current_user_id;
                $order_process->save();
                DB::commit();
                return true;
            }
            return false;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getOrderProcessList()
    {
        try {
            $validator = Validator::make($this->data, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $current_user_id = $this->current_user->id;
                $order_processes = OrderProcess::where('is_deleted', false)
                    ->where('created_by', $current_user_id)
                    ->orderBy('updated_at', 'desc')->get();
                $order_processes->load(['created_by', 'updated_by', 'customer_group']);
                foreach ($order_processes as $order_process_item) {
                    $order_process_item['total_so_count'] = $order_process_item->so_headers->count();
                    $order_process_item['synchronized_so_count'] = $order_process_item->synchronized_so_headers->count();
                    unset($order_process_item->so_headers, $order_process_item->synchronized_so_headers);
                }
                return $order_processes;
            }
            return false;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
