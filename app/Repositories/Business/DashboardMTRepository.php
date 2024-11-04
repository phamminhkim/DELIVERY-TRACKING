<?php

namespace App\Repositories\Business;

use App\Models\Business\OrderProcess;
use App\Models\Business\PublicHoliday;
use App\Models\Business\RawPoDataItem;
use App\Models\Business\RawPoHeader;
use App\Repositories\Abstracts\RepositoryAbs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Business\SoHeader;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapMaterialMapping;
use App\Models\Master\SapUnit;
use App\Services\Sap\SapApiHelper;

class DashboardMTRepository extends RepositoryAbs
{

    public function getPoByUser()
    {
        try {
            // Kiểm tra quyền người dùng
            if (!$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }

            // Khởi tạo biến ngày mặc định là từ đầu đến cuối tháng hiện tại
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();

            // Nếu có from_date và to_date thì ghi đè lên khoảng ngày mặc định
            if ($this->request->filled('from_date')) {
                $startDate = Carbon::parse($this->request->from_date)->startOfDay();
            }
            if ($this->request->filled('to_date')) {
                $endDate = Carbon::parse($this->request->to_date)->endOfDay();
            }

            // Lấy danh sách user_ids từ yêu cầu (sử dụng null nếu không có)
            $user_ids = $this->request->filled('user_ids') ? $this->request->user_ids : null;

            // Khởi tạo truy vấn lấy danh sách người dùng và các thông tin liên quan đến đơn hàng
            $query = DB::table('users')
                ->leftJoin('order_processes', function ($join) {
                    $join->on('users.id', '=', 'order_processes.created_by')
                        ->where('order_processes.is_deleted', '=', 0);
                })
                ->leftJoin('so_headers', function ($join) use ($startDate, $endDate) {
                    $join->on('order_processes.id', '=', 'so_headers.order_process_id')
                        ->whereBetween('so_headers.created_at', [$startDate, $endDate]);
                })
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('COUNT(so_headers.id) as total_orders'),
                    DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 1 THEN 1 ELSE 0 END) as synced_orders'),
                    DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 0 THEN 1 ELSE 0 END) as unsynced_orders')
                )
                ->groupBy('users.id', 'users.name');
            // Nếu user_ids có giá trị, thêm điều kiện whereIn để lọc theo user_ids
            if ($user_ids) {
                $query->whereIn('users.id', $user_ids);
            } else {
                // Nếu không có user_ids, chỉ lấy người dùng có liên kết với order_processes
                $query->whereNotNull('order_processes.created_by');
            }

            // Lọc theo trạng thái đơn hàng
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                if (is_array($sync_sap_status)) {
                    $query->where(function ($q) use ($sync_sap_status) {
                        $q->whereIn('so_headers.sync_sap_status', $sync_sap_status)
                            ->orWhereNull('so_headers.sync_sap_status');
                    });
                }
            }

            // Lọc theo nhóm đơn hàng
            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                if (is_array($customer_group_ids)) {
                    $query->whereIn('order_processes.customer_group_id', $customer_group_ids);
                }
            }

            // Thực thi truy vấn và lấy kết quả
            $result = $query->get()->keyBy('id');

            // Tạo mảng chứa kết quả cuối cùng
            $users = [];
            $total_orders = [];
            $synced_orders = [];
            $unsynced_orders = [];

            // Nếu có user_ids đầu vào, duyệt qua để đảm bảo trả về đủ tất cả người dùng
            if ($user_ids) {
                foreach ($user_ids as $user_id) {
                    if (isset($result[$user_id])) {
                        $users[] = $result[$user_id]->name;
                        $total_orders[] = (int) $result[$user_id]->total_orders;
                        $synced_orders[] = (int) $result[$user_id]->synced_orders;
                        $unsynced_orders[] = (int) $result[$user_id]->unsynced_orders;
                    } else {
                        $user = DB::table('users')->find($user_id);
                        $users[] = $user ? $user->name : $user_id;
                        $total_orders[] = 0;
                        $synced_orders[] = 0;
                        $unsynced_orders[] = 0;
                    }
                }
            } else {
                // Nếu không có user_ids, thêm tất cả user từ kết quả truy vấn
                foreach ($result as $user) {
                    $users[] = $user->name;
                    $total_orders[] = (int) $user->total_orders;
                    $synced_orders[] = (int) $user->synced_orders;
                    $unsynced_orders[] = (int) $user->unsynced_orders;
                }
            }

            // Trả về dữ liệu dưới dạng JSON
            return [
                'users' => $users,
                'total_orders' => $total_orders,
                'synced_orders' => $synced_orders,
                'unsynced_orders' => $unsynced_orders
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function getPoByCustomerGroup()
    {
        try {
            $user = auth()->user();

            if (!$user || !$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }

            // Khởi tạo biến ngày mặc định là từ đầu đến cuối tháng hiện tại
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();

            // Nếu có from_date và to_date thì ghi đè lên khoảng ngày mặc định
            if ($this->request->filled('from_date')) {
                $startDate = Carbon::parse($this->request->from_date)->startOfDay();
            }
            if ($this->request->filled('to_date')) {
                $endDate = Carbon::parse($this->request->to_date)->endOfDay();
            }

            // Tạo truy vấn sử dụng LEFT JOIN để lấy tất cả các nhóm khách hàng
            $query = DB::table('customer_groups')
                ->leftJoin('order_processes', function ($join) {
                    $join->on('customer_groups.id', '=', 'order_processes.customer_group_id')
                        ->where('order_processes.is_deleted', 0); // Chỉ lấy những quy trình đặt hàng không bị xóa
                })
                ->leftJoin('so_headers', function ($join) use ($startDate, $endDate) {
                    $join->on('order_processes.id', '=', 'so_headers.order_process_id')
                        ->whereBetween('so_headers.created_at', [$startDate, $endDate]); // Lọc theo khoảng thời gian
                })
                ->select('customer_groups.name', DB::raw('COUNT(so_headers.id) as total_orders'))
                ->groupBy('customer_groups.id', 'customer_groups.name');
            // Lọc theo trạng thái đồng bộ SAP
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->where(function ($q) use ($sync_sap_status) {
                    $q->whereIn('so_headers.sync_sap_status', $sync_sap_status)
                        ->orWhereNull('so_headers.sync_sap_status'); // Bao gồm cả những nhóm không có đơn hàng
                });
            }

            // Lọc theo các nhóm khách hàng được chọn
            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->whereIn('customer_groups.id', $customer_group_ids);
            }

            // Lọc theo người tạo đơn hàng
            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->where(function ($q) use ($user_ids) {
                    $q->whereIn('order_processes.created_by', $user_ids)
                        ->orWhereNull('order_processes.created_by'); // Bao gồm cả những nhóm không có đơn hàng
                });
            }

            $soHeaders = $query->get();

            // Tạo hai mảng để chứa tên nhóm khách hàng và tổng số đơn hàng
            $group = [];
            $total = [];

            // Lặp qua kết quả và thêm vào mảng
            foreach ($soHeaders as $header) {
                $group[] = $header->name; // Tên của nhóm khách hàng
                $total[] = (int) $header->total_orders; // Tổng số đơn hàng (có thể là 0 nếu không có đơn hàng)
            }

            return [
                'group' => $group,
                'total' => $total
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getPoByDate()
    {
        try {
            $user = auth()->user();

            // Kiểm tra quyền truy cập
            if (!$user || !$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }

            // Khởi tạo biến ngày mặc định là từ đầu đến cuối tháng hiện tại
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();

            // Nếu có from_date và to_date thì ghi đè lên khoảng ngày mặc định
            if ($this->request->filled('from_date')) {
                $startDate = Carbon::parse($this->request->from_date)->startOfDay();
            }
            if ($this->request->filled('to_date')) {
                $endDate = Carbon::parse($this->request->to_date)->endOfDay();
            }

            // Khởi tạo truy vấn
            $query = DB::table('order_processes')
                ->join('users', 'order_processes.created_by', '=', 'users.id')
                ->leftJoin('so_headers', function ($join) use ($startDate, $endDate) {
                    $join->on('order_processes.id', '=', 'so_headers.order_process_id')
                        ->whereBetween('so_headers.created_at', [$startDate, $endDate]);
                })
                ->where('order_processes.is_deleted', '=', 0)
                ->select(DB::raw('DATE(so_headers.created_at) as date'), DB::raw('COUNT(so_headers.id) as total_orders'))
                ->groupBy(DB::raw('DATE(so_headers.created_at)'));

            // Lọc theo trạng thái
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->whereIn('so_headers.sync_sap_status', $sync_sap_status);
            }
            // Lọc theo nhóm khách hàng
            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->whereIn('order_processes.customer_group_id', $customer_group_ids);
            }
            // Lọc theo user
            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->whereIn('order_processes.created_by', $user_ids);
            }

            // Thực hiện truy vấn và lấy kết quả
            $soHeader = $query->get();

            // Tạo mảng ngày và tổng số đơn hàng
            $dateRange = collect(\Carbon\CarbonPeriod::create($startDate, $endDate));
            $dates = [];
            $total = [];
            // Duyệt qua khoảng ngày để đối chiếu đơn hàng với các ngày cụ thể
            foreach ($dateRange as $date) {
                // Lọc các đơn hàng cho ngày cụ thể sử dụng thuộc tính 'date' thay vì 'created_at'
                $ordersOnDate = $soHeader->firstWhere('date', $date->format('Y-m-d'));
                $dates[] = $date->format('d/m/Y'); // định dạng ngày 'dd/mm/yyyy'
                $total[] = $ordersOnDate ? $ordersOnDate->total_orders : 0; // Tổng số đơn hàng trong ngày
            }

            return [
                "date" => $dates,
                "total" => $total
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getPOStatistics()
    {
        try {
            // Kiểm tra quyền người dùng
            if (!$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }

            // Khởi tạo biến ngày mặc định là từ đầu đến cuối tháng hiện tại
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();

            // Nếu có from_date và to_date thì ghi đè lên khoảng ngày mặc định
            if ($this->request->filled('from_date')) {
                $startDate = Carbon::parse($this->request->from_date)->startOfDay();
            }
            if ($this->request->filled('to_date')) {
                $endDate = Carbon::parse($this->request->to_date)->endOfDay();
            }

            // Khởi tạo truy vấn sử dụng LEFT JOIN để đảm bảo lấy tất cả người dùng
            $query = DB::table('order_processes')
                ->join('users', 'order_processes.created_by', '=', 'users.id')
                ->leftJoin('so_headers', function ($join) use ($startDate, $endDate) {
                    $join->on('order_processes.id', '=', 'so_headers.order_process_id')
                        ->whereBetween('so_headers.created_at', [$startDate, $endDate]);
                })
                ->where('order_processes.is_deleted', '=', 0)
                ->select(
                    DB::raw('COUNT(DISTINCT users.id) as total_users'), // Tổng số người dùng
                    DB::raw('COUNT(so_headers.id) as total_orders'), // Tổng số đơn hàng
                    DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 1 THEN 1 ELSE 0 END) as synced_orders'), // Đơn đã đồng bộ
                    DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 0 THEN 1 ELSE 0 END) as unsynced_orders') // Đơn chưa đồng bộ
                );
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->whereIn('so_headers.sync_sap_status', $sync_sap_status);
            }
            // Thêm điều kiện lọc nếu có các trường khác
            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->whereIn('order_processes.customer_group_id', $customer_group_ids);
            }

            // Nếu có user_ids, cũng lọc theo user đã chọn
            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->whereIn('order_processes.created_by', $user_ids);
            }

            // Thực hiện truy vấn và lấy kết quả
            $result = $query->first();

            return  [
                'total_users' => $result->total_users ?? 0, // Tổng số người dùng
                'total_orders' => $result->total_orders ?? 0, // Tổng số đơn hàng
                'synced_orders' => $result->synced_orders ?? 0, // Tổng số đơn hàng đã đồng bộ
                'unsynced_orders' => $result->unsynced_orders ?? 0 // Tổng số đơn hàng chưa đồng bộ
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function compareOrderReports()
    {
        try {
            // Kiểm tra quyền người dùng
            if (!$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }
            // Bước 1: Lấy dữ liệu báo cáo đơn hàng
            $soHeaders = $this->getOrderReports();
            // Bước 2: Xử lý ánh xạ với vật liệu SAP
            $result = $this->mapItemsToSapMaterials($soHeaders);
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    protected function getOrderReports()
    {
        // Chuẩn bị truy vấn cho báo cáo đơn hàng
        $query = SoHeader::query();
        $query->whereNotNull('so_uid')
            ->whereHas('order_process', function ($query) {
                $query->where('is_deleted', 0);
            });
        // thực hiện lọc dữ liệu
        if ($this->request->filled('ids')) {
            $query->whereIn('id', $this->request->ids);
        }
        if ($this->request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $this->request->from_date);
        }
        if ($this->request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $this->request->to_date);
        }
        if ($this->request->filled('so_uid')) {
            $query->where('so_uid', 'LIKE', '%' . $this->request->so_uid . '%');
        }
        if ($this->request->filled('order_process_id')) {
            $query->whereIn('order_process_id', $this->request->order_process_id);
        }
        if ($this->request->filled('po_number')) {
            $query->where('po_number', 'LIKE', '%' . $this->request->po_number . '%');
        }
        if ($this->request->filled('sap_codes')) {
            $sap_codes = $this->request->sap_codes; // Sử dụng sap_codes từ request
            $query->whereHas('order_process', function ($query) use ($sap_codes) {
                $query->whereHas('customer_group', function ($query) use ($sap_codes) {
                    $query->whereHas('customer_materials', function ($query) use ($sap_codes) {
                        $query->whereHas('sap_materials', function ($query) use ($sap_codes) {
                            $query->whereIn('sap_code', $sap_codes); // Tìm kiếm bằng whereIn
                        });
                    });
                });
            });
        }
        if ($this->request->filled('customer_name')) {
            $query->where('customer_name', 'LIKE', '%' . $this->request->customer_name . '%');
        }
        if ($this->request->filled('customer_code')) {
            $query->where('customer_code', 'LIKE', '%' . $this->request->customer_code . '%');
        }
        if ($this->request->filled('created_bys')) {
            $query->whereHas('order_process', function ($query) {
                $query->whereIn('created_by', $this->request->created_bys);
            });
        }
        if ($this->request->filled('customer_group_ids')) {
            $query->whereHas('order_process', function ($query) {
                $query->whereIn('customer_group_id', $this->request->customer_group_ids);
            });
        }
        if ($this->request->filled('customer_codes')) {
            $query->whereHas('customer_partners', function ($query) {
                $query->whereIn('id', $this->request->customer_codes);
            });
        }

        // Lấy dữ liệu báo cáo đơn hàng
        return $query->with([
            'order_process.customer_group:id,name',
            'order_process.created_by:id,name',
            'raw_po_header.raw_po_data_items',
        ])->orderByDesc('id')->get();
    }

    protected function mapItemsToSapMaterials($soHeaders)
    {
        $result = new \stdClass(); // Tạo một đối tượng rỗng
        $result->so_items = []; // Khởi tạo thuộc tính so_items là một mảng

        $poNumbers = array_column(is_array($soHeaders) ? $soHeaders : $soHeaders->toArray(), 'po_number');

        // Truy vấn các RawPoHeader theo po_numbers với chunk để giảm tải bộ nhớ
        $rawPoHeaders = [];
        RawPoHeader::whereIn('po_number', $poNumbers)
            ->chunk(1000, function ($chunk) use (&$rawPoHeaders) {
                foreach ($chunk as $rawPoHeader) {
                    $rawPoHeaders[$rawPoHeader->po_number][] = $rawPoHeader;
                }
            });

        // Truy vấn tất cả các RawPoDataItem với chunk
        $rawPoHeaderIds = collect($rawPoHeaders)->flatten()->pluck('id')->toArray();
        $rawPoDataItems = [];
        RawPoDataItem::whereIn('raw_po_header_id', $rawPoHeaderIds)
            ->chunk(1000, function ($chunk) use (&$rawPoDataItems) {
                foreach ($chunk as $item) {
                    $rawPoDataItems[$item->raw_po_header_id][] = $item;
                }
            });
        foreach ($soHeaders as $soHeader) {
            // Khởi tạo dữ liệu cơ bản từ soHeader
            $data = new \stdClass(); // Tạo một đối tượng rỗng cho mỗi soHeader
            $data->so_items = []; // Khởi tạo thuộc tính so_items là một mảng
            // Lấy RawPoHeader cuối cùng và các RawPoDataItem liên quan
            $poHeaders = $rawPoHeaders[$soHeader->po_number] ?? [];
            $lastRawPoHeaderId = end($poHeaders)->id ?? null;
            $itemsArray = $rawPoDataItems[$lastRawPoHeaderId] ?? [];
            // Chuẩn bị dữ liệu cho SAP Material và SAP Material Mapping
            $customer_group_id = optional($soHeader->order_process->customer_group)->id;
            $customerSkuCodes = array_column($itemsArray, 'customer_sku_code');
            // Truy vấn các SAP Materials với chunk để giảm tải bộ nhớ
            $sapMaterials = [];
            SapMaterial::whereIn('bar_code', $customerSkuCodes)
                ->where('is_deleted', 0)
                ->orderByRaw('CASE WHEN priority IS NOT NULL THEN 0 ELSE 1 END, priority ASC')
                ->chunk(1000, function ($chunk) use (&$sapMaterials) {
                    foreach ($chunk as $sapMaterial) {
                        if (!isset($sapMaterials[$sapMaterial->bar_code]) || $sapMaterial->priority < $sapMaterials[$sapMaterial->bar_code]->priority) {
                            $sapMaterials[$sapMaterial->bar_code] = $sapMaterial;
                        }
                    }
                });

            // Truy vấn các SAP Material Mapping với chunk
            $sapMaterialMappings = [];
            SapMaterialMapping::whereHas('customer_material', function ($query) use ($customer_group_id, $customerSkuCodes) {
                $query->where('customer_group_id', $customer_group_id)
                    ->whereIn('customer_sku_code', $customerSkuCodes);
            })->chunk(1000, function ($chunk) use (&$sapMaterialMappings) {
                foreach ($chunk as $mapping) {
                    $sapMaterialMappings[$mapping->customer_material->customer_sku_code][] = $mapping;
                }
            });
            // Mapping dữ liệu cho từng item
            $soItems = [];
            foreach ($itemsArray as $item) {
                $customer_sku_code = $item['customer_sku_code'];
                $foundMapping = false;

                if (isset($sapMaterials[$customer_sku_code])) {
                    $sapMaterial = $sapMaterials[$customer_sku_code];
                    if ($sapMaterial->is_deleted == 0) {
                        $item['sku_sap_code'] = $sapMaterial->sap_code;
                        $item['sku_sap_name'] = $sapMaterial->name;
                        $item['sku_sap_unit'] = SapUnit::find($sapMaterial->unit_id)->unit_code ?? null;
                        $item['quantity3_sap'] = $item['quantity2_po'];
                        $soItems[] = $item;
                        $foundMapping = true;
                    }
                }
                if (!$foundMapping && isset($sapMaterialMappings[$customer_sku_code])) {
                    foreach ($sapMaterialMappings[$customer_sku_code] as $sapMaterialMapping) {
                        $sapMaterial = SapMaterial::find($sapMaterialMapping->sap_material_id);
                        if ($sapMaterial) {
                            $quantity3_sap = (($item['quantity2_po'] * $sapMaterialMapping->conversion_rate_sap) / $sapMaterialMapping->customer_number) * ($sapMaterialMapping->percentage / 100);
                            $item['sku_sap_code'] = $sapMaterial->sap_code;
                            $item['sku_sap_name'] = $sapMaterial->name;
                            $item['sku_sap_unit'] = SapUnit::find($sapMaterial->unit_id)->unit_code ?? null;
                            $item['quantity3_sap'] = $quantity3_sap;
                            $soItems[] = $item;
                            $foundMapping = true;
                            break;
                        }
                    }
                }
                if (!$foundMapping) {
                    $item['sku_sap_code'] = null;
                    $item['sku_sap_name'] = null;
                    $item['sku_sap_unit'] = null;
                    $item['quantity3_sap'] = $item['quantity2_po'];
                    $soItems[] = $item;
                }
            }

            // Tính toán fulfillment_rate từ SAP API và xử lý dữ liệu
            $sapData = [
                "ID" => "1001",
                "action_name" => "FETCH_SALESORDERS",
                "BODY" => [
                    ["VBELN" => $soHeader->so_uid]
                ]
            ];
            $json = SapApiHelper::postData(json_encode($sapData));
            $jsonData = json_decode(json_encode($json), true);

            // Kiểm tra lỗi kết nối đồng bộ
            if (!$jsonData['success']) {
                $this->errors['sap_error'] = $jsonData['error'];
                return null; // Trả về null nếu có lỗi
            } else {
                if (!empty($jsonData['data'])) {
                    foreach ($jsonData['data'] as $json_value) {
                        $sapItems = $json_value['ITEMS'] ?? [];
                        foreach ($sapItems as $item_sap) {
                            foreach ($soItems as &$item) {
                                $sapQuantity = $item_sap['SAP_QUANTITY'] ?? null;
                                $quantity3_sap = $item['quantity3_sap'] ?? null;

                                $fulfillmentRate = (!empty($quantity3_sap) && !empty($sapQuantity))
                                    ? round(($sapQuantity / $quantity3_sap) * 100)
                                    : 0;
                                // Làm tròn fulfillment_rate đến số nguyên gần nhất
                                $fulfillmentRate = round($fulfillmentRate);
                                // Chia cho 100 nếu fulfillmentRate lớn hơn 100
                                if ($fulfillmentRate > 100) {
                                    $fulfillmentRate = $fulfillmentRate / 100; // Chia cho 100
                                }
                                // Cập nhật các giá trị khác
                                $item['sap_code'] = $item_sap['SAP_CODE'] ?? null;
                                $item['sap_name'] = $item_sap['SAP_NAME'] ?? null;
                                $item['sap_quantity'] = $sapQuantity;
                                $item['sap_unit_code'] = $item_sap['UNIT_CODE'] ?? null;
                                $item['sap_user'] = $item_sap['USER'] ?? null;
                                $item['fulfillment_rate'] = $fulfillmentRate;
                            }
                        }
                    }
                }
            }
            // Cập nhật dữ liệu cho từng item từ soHeader
            foreach ($soItems as $item) {
                $updatedItem = new \stdClass(); // Tạo một đối tượng mới cho mỗi item
                $updatedItem->id = $soHeader->id;
                $updatedItem->customer_code = $soHeader->customer_code;
                $updatedItem->customer_name = $soHeader->customer_name;
                $updatedItem->po_number = $soHeader->po_number;
                $updatedItem->so_uid = $soHeader->so_uid;
                $updatedItem->created_at = $soHeader->created_at;
                $updatedItem->order_process = new \stdClass(); // Tạo đối tượng cho order_process
                $updatedItem->order_process->id = $soHeader->order_process->id;
                $updatedItem->order_process->customer_group_name = $soHeader->order_process->customer_group->name;
                $updatedItem->order_process->created_by = $soHeader->order_process->created_by;
                $updatedItem->raw_po_header_id = $item['raw_po_header_id'];
                $updatedItem->customer_sku_code = $item['customer_sku_code'];
                $updatedItem->customer_sku_name = $item['customer_sku_name'];
                $updatedItem->customer_sku_unit = $item['customer_sku_unit'];
                $updatedItem->quantity2_po = $item['quantity2_po'];
                $updatedItem->sku_sap_code = $item['sku_sap_code'];
                $updatedItem->sku_sap_name = $item['sku_sap_name'];
                $updatedItem->sku_sap_unit = $item['sku_sap_unit'];
                $updatedItem->quantity3_sap = $item['quantity3_sap'];
                $updatedItem->sap_code = $item['sap_code'];
                $updatedItem->sap_name = $item['sap_name'];
                $updatedItem->sap_quantity = $item['sap_quantity'];
                $updatedItem->sap_unit_code = $item['sap_unit_code'];
                $updatedItem->sap_user = $item['sap_user'];
                $updatedItem->fulfillment_rate = $item['fulfillment_rate'];

                $data->so_items[] = $updatedItem; // Thêm item vào mảng so_items
            }
            $result->so_items = array_merge($result->so_items, $data->so_items); // Thêm so_items từ data vào result
        }
        return $result;
    }
}
