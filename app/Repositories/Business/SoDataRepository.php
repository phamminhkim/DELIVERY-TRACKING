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
use Illuminate\Pagination\LengthAwarePaginator;

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
                                'convert_po_uid' => isset($so_items[0]['convert_file_uid']) ? $so_items[0]['convert_file_uid'] : null,
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
                $order_process->so_data_items->each(function ($so_data_item) {
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
                            'convert_po_uid' => isset($so_items[0]['convert_file_uid']) ? $so_items[0]['convert_file_uid'] : null,
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
                // Kiểm tra có tồn tại đơn hàng đã hoặc đang đồng bộ SAP
                if ($order_process->sync_so_headers->count() > 0) {
                    $this->errors = "Tồn tại đơn hàng đã hoặc đang được đồng bộ SAP";
                    return false;
                }
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

    public function deleteMultipleSo()
    {
        try {
            $validator = Validator::make($this->data, [
                'so_header_ids' => 'required|array',
                'so_header_ids.*' => 'required|integer',
            ], [
                'so_header_ids.required' => 'Danh sách id là bắt buộc',
                'so_header_ids.array' => 'Danh sách id phải là mảng',
                'so_header_ids.*.required' => 'Id là bắt buộc',
                'so_header_ids.*.integer' => 'Id phải là số nguyên',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                DB::beginTransaction();
                $so_header_ids = $this->data['so_header_ids'];
                $so_headers = SoHeader::whereIn('id', $so_header_ids)->get();
                // Lấy so_header đã hoặc đang đồng bộ
                $sync_so_headers = $so_headers->filter(function ($so_header) {
                    return $so_header->so_uid || $so_header->is_syncing_sap;
                });
                // Kiểm tra tất cả đơn đã hoặc đang đồng bộ SAP
                if ($sync_so_headers->count() > 0) {
                    $this->errors = "Tồn tại đơn hàng đã hoặc đang được đồng bộ SAP";
                    // Trả về các đơn đã hoặc đang đồng bộ SAP
                    $this->message['sync_so_headers'] = $sync_so_headers->map(function ($so_header) {
                        return [
                            'id' => $so_header->id,
                            'sap_so_number' => $so_header->sap_so_number,
                            'so_uid' => $so_header->so_uid,
                            'is_syncing_sap' => $so_header->is_syncing_sap,
                        ];
                    });

                    return false;
                }
                // Xóa so_header
                $so_headers->each(function ($so_header) {
                    if ($so_header->theme_color) {
                        $so_header->theme_color->delete();
                    }
                });
                $so_headers->each(function ($so_header) {
                    $so_header->so_data_items->each(function ($so_data_item) {
                        if ($so_data_item->theme_color) {
                            $so_data_item->theme_color->delete();
                        }
                    });
                });
                $so_headers->each(function ($so_header) {
                    $so_header->so_data_items()->delete();
                });
                $so_headers->each(function ($so_header) {
                    $so_header->delete();
                });
                DB::commit();
                return true;
            }
            return false;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    public function getOrderProcessList()
    {
        try {
            $validator = Validator::make($this->data, [], []);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                return false;
            }

            $search = $this->request->get('search', '');
            $sort_field = $this->request->get('sort_field', 'updated_at');
            $sort_direction = $this->request->get('sort_direction', 'desc');
            $per_page = $this->request->get('per_page', 'All');
            $sort_field = $sort_field ?: 'updated_at';
            $sort_direction = $sort_direction ?: 'desc';
            $current_user_id = $this->current_user->id;
            $query = OrderProcess::query();
            $query->where('is_deleted', false);
            if (!$this->current_user->hasRole(['admin-order-process'])) {
                $query->where('created_by', $current_user_id);
            }
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('serial_number', 'like', "%{$search}%")
                        // ->orWhere('customer_group_id', 'like', "%{$search}%")
                        ->orWhereHas('customer_group', function ($qu) use ($search) {
                            $qu->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('title', 'like', "%{$search}%")
                        // ->orWhere('created_by', 'like', "%{$search}%")
                        ->orWhereHas('created_by', function ($qu) use ($search) {
                            $qu->where('name', 'like', "%{$search}%");
                        })
                        // ->orWhere('updated_by', 'like', "%{$search}%");
                        ->orWhereHas('updated_by', function ($qu) use ($search) {
                            $qu->where('name', 'like', "%{$search}%");
                        });
                });
            }
            // Sắp xếp theo field thuộc bảng order_proccess
            if ($sort_field !== 'synchronized_so_count') {
                $query->orderBy($sort_field, $sort_direction);
            }
            $order_processes = $query->get();
            $order_processes->load(['created_by', 'updated_by', 'customer_group']);
            foreach ($order_processes as $order_process_item) {
                $order_process_item['total_so_count'] = $order_process_item->so_headers->count();
                $order_process_item['synchronized_so_count'] = $order_process_item->synchronized_so_headers->count();
                unset($order_process_item->synchronized_so_headers);
                // Duyệt $order_process_item->so_headers để lấy thêm promotive_name
                $order_process_item->so_headers->each(function ($so_header) {
                    if ($so_header->so_data_items->count() > 0) {
                        $so_header['promotive_name'] = $so_header->so_data_items->first()->promotive_name;
                    } else {
                        $so_header['promotive_name'] = null;
                    }
                    unset($so_header->so_data_items);
                });
            }
            // Sắp xếp theo synchronized_so_count phát sinh ngoài bảng order_proccess
            if ($sort_field == 'synchronized_so_count') {
                $order_processes = $order_processes->sortBy(function ($order_process_item) {
                    return $order_process_item['synchronized_so_count'];
                }, SORT_REGULAR, $sort_direction == 'desc');
            }
            // Phân trang kết quả
            if ($per_page !== 'All') {
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $order_processes = new LengthAwarePaginator(
                    $order_processes->forPage($currentPage, $per_page),
                    $order_processes->count(),
                    $per_page,
                    $currentPage,
                    ['path' => LengthAwarePaginator::resolveCurrentPath()]
                );
            }
            return $order_processes;
        } catch (\Throwable $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
