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
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

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
    
            // Kiểm tra nếu mapItemsToSapMaterials trả về lỗi SAP
            if (isset($this->errors['sap_error'])) {
                // Nếu có lỗi "sap_error" từ SAP, hiển thị lỗi này
                return false;
            }    
            // Kiểm tra nếu $result không có dữ liệu (là null hoặc rỗng)
            if (is_null($result) || (is_array($result) && empty($result)) || (is_object($result) && $result->isEmpty())) {
                return collect([]); // Trả về tập rỗng nếu không có dữ liệu
            }    
            return $result; // Trả về kết quả nếu có dữ liệu
    
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
        $query->whereNotNull('so_uid')->whereNotNull('convert_po_uid');

        // Lọc dữ liệu theo các điều kiện từ request
        if ($this->request->filled('ids')) {
            $query->whereIn('id', $this->request->ids);
        }
        // filter theo Số SO SAP
        if ($this->request->filled('so_uid')) {
            $query->where('so_uid', 'LIKE', '%' . $this->request->so_uid . '%');
        }
        if ($this->request->filled('order_process_id')) {
            $query->whereIn('order_process_id', $this->request->order_process_id);
        }
        if ($this->request->filled('po_number')) {
            $query->where('po_number', 'LIKE', '%' . $this->request->po_number . '%');
        }
        // filter theo PO mã SAP
        if ($this->request->filled('sap_codes')) {
            $sap_codes = explode(',', $this->request->sap_codes);
            $sap_codes = array_map('trim', $sap_codes);
            $query->whereHas('order_process', function ($query) use ($sap_codes) {
                $query->whereHas('customer_group', function ($query) use ($sap_codes) {
                    $query->whereHas('customer_materials', function ($query) use ($sap_codes) {
                        $query->whereHas('sap_materials', function ($query) use ($sap_codes) {
                            $query->whereIn('sap_code', $sap_codes);
                        });
                    });
                });
            });
        }
        // Lọc theo tên khách hàng
        if ($this->request->filled('customer_name')) {
            $customer_name = $this->request->customer_name;
            $query->where('customer_name', 'LIKE', '%' . $customer_name . '%');
        }

        // Lọc theo mã khách hàng
        if ($this->request->filled('customer_code')) {
            $customer_code = $this->request->customer_code;
            $query->where('customer_code', 'LIKE', '%' . $customer_code . '%');
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

        // Khởi tạo khoảng thời gian mặc định là 1 tuần gần nhất
        $endDate = now()->endOfDay();
        $startDate = now()->subDays(7)->startOfDay();

        // Nếu có `from_date` và `to_date`, ghi đè lên khoảng ngày mặc định
        if ($this->request->filled('from_date')) {
            $startDate = Carbon::create($this->request->from_date)->startOfDay();
        }
        if ($this->request->filled('to_date')) {
            $endDate = Carbon::create($this->request->to_date)->endOfDay();
        }

        // Áp dụng điều kiện thời gian vào truy vấn
        $query->whereBetween('created_at', [$startDate, $endDate]);

        // Lấy dữ liệu báo cáo đơn hàng
        return $query->with([
            'order_process.customer_group:id,name',
            'order_process.created_by:id,name',
        ])->orderByDesc('id')->get();
    }


    protected function mapItemsToSapMaterials($soHeaders)
    {
        $startTime = microtime(true);
        $result = (object) null;
        $result->so_items = []; // Khởi tạo thuộc tính so_items là một mảng

        $convertPoUids  = array_column(is_array($soHeaders) ? $soHeaders : $soHeaders->toArray(), 'convert_po_uid');
        $chunk_size = 1000; // Kích thước mảng con
        $rawPoHeaders = collect();
        foreach (array_chunk($convertPoUids, $chunk_size) as $chunk) {
            $partialResults = RawPoHeader::whereIn('convert_po_uid', $chunk)
                ->select('id','po_number', 'convert_po_uid', 'customer_name', 'customer_code', 'sap_so_number')
                ->get()
                ->keyBy('id');
            $rawPoHeaders = $rawPoHeaders->merge($partialResults);
        }


        // Remove duplicate po_number và lấy ra id duy nhất
        $rawPoHeaderIds = $rawPoHeaders->pluck('id')->toArray();
        $rawPoHeaders = $rawPoHeaders->toArray();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy rawPoHeaders: " . $executionTime . " giây");
        $startTime = microtime(true);
        $rawPoDataItems = collect();
        foreach (array_chunk($rawPoHeaderIds, $chunk_size) as $chunk) {
            $partialResults = RawPoDataItem::whereIn('raw_po_header_id', $chunk)
                ->select('id', 'raw_po_header_id', 'customer_sku_code','customer_sku_name','customer_sku_unit', 'quantity2_po')
                ->get()
                ->keyBy('id');
            $rawPoDataItems = $rawPoDataItems->merge($partialResults);
        }
        $rawPoDataItemIds = $rawPoDataItems->pluck('id')->toArray();
        $rawPoDataItemSkuCodes = $rawPoDataItems
            ->pluck('customer_sku_code')
            ->filter() // Loại bỏ các phần tử rỗng
            ->toArray();
        $rawPoDataItems = $rawPoDataItems->toArray();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy rawPoDataItems: " . $executionTime . " giây");
        $startTime = microtime(true);

        // Lấy bảng SAP có item liên quan
        $sapMaterials = collect();
        foreach (array_chunk($rawPoDataItemSkuCodes, $chunk_size) as $chunk) {
            $partialResults = SapMaterial::whereIn('bar_code', $chunk)
                ->where('is_deleted', 0)
                ->orderByRaw('CASE WHEN priority IS NOT NULL THEN 0 ELSE 1 END, priority ASC')
                ->select('id','bar_code', 'sap_code', 'name', 'unit_id')
                ->get()
                ->keyBy('id');
            $sapMaterials = $sapMaterials->merge($partialResults);
        }
        $sapMaterials = $sapMaterials->toArray();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy sapMaterials: " . $executionTime . " giây");
        $startTime = microtime(true);

        // Lấy bảng customer_materials liên quan
        $customerMaterials = collect();
        foreach (array_chunk($rawPoDataItemSkuCodes, $chunk_size) as $chunk) {
            $partialResults = CustomerMaterial::whereIn('customer_sku_code', $chunk)
                ->select('id','customer_group_id', 'customer_sku_code')
                ->get()
                ->keyBy('id');
            $customerMaterials = $customerMaterials->merge($partialResults);
        }
        $customerMaterialIds = $customerMaterials->pluck('id')->toArray();
        $customerMaterials = $customerMaterials->toArray();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy customerMaterials: " . $executionTime . " giây");
        $startTime = microtime(true);

        // Lấy bảng mapping SAP liên quan
        $sapMaterialMappings = collect();
        foreach (array_chunk($customerMaterialIds, $chunk_size) as $chunk) {
            $partialResults = SapMaterialMapping::whereIn('customer_material_id', $customerMaterialIds)
                ->select('id','customer_material_id', 'customer_number', 'sap_material_id', 'conversion_rate_sap', 'percentage')
                ->get()
                ->keyBy('id');
            $sapMaterialMappings = $sapMaterialMappings->merge($partialResults);
        }
        $sapMaterialMappings = $sapMaterialMappings->toArray();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy sapMaterialMappings: " . $executionTime . " giây");
        $startTime = microtime(true);

        Log::info("Số lượng rawPoDataItems: " . count($rawPoDataItems) . " items");
        $totalMTSoItems = [];
        foreach ($soHeaders as $soHeader) {
            $so_uid = $soHeader->so_uid;
            $po_number = $soHeader->po_number;
            $so_data_item = $soHeader->so_data_items->first();
            $promotive_name = $so_data_item->promotive_name;
            if (!empty($promotive_name)) {
                // Đối với đơn khuyến mãi thì bỏ qua
                continue;
            }
            $customer_group_id = $soHeader->order_process->customer_group_id;

            $totalMTSoItems[$so_uid] = array();
            $itemsArray = [];
            $convert_po_uid = $soHeader->convert_po_uid;
            $poHeaders = array_filter($rawPoHeaders, function($rawPoHeader) use ($convert_po_uid, $po_number) {
                return $rawPoHeader['convert_po_uid'] == $convert_po_uid && $rawPoHeader['po_number'] == $po_number;
            });
            // Nếu $poHeaders là mảng rỗng thì continue vòng lặp
            if (count($poHeaders) == 0) {
                continue;
            }
            if ($customer_group_id != 11) {
                // Không phải đơn bổ sung thì convert_po_uid là duy nhất
                $lastPoHeader = reset($poHeaders);
                $lastPoHeaderId = $lastPoHeader['id'];
                $itemsArray = array_filter($rawPoDataItems, function($rawPoDataItem) use ($lastPoHeaderId) {
                    return $rawPoDataItem['raw_po_header_id'] == $lastPoHeaderId;
                });
            } else {
                // Đơn bổ sung thì lấy item trên data final
                $finalSoHeader = SoHeader::find($soHeader->id);
                $finalSoHeader->so_data_items->each(function($soDataItem) use (&$itemsArray) {
                    $itemsArray[] = [
                        'id' => $soDataItem->id,
                        'customer_sku_code' => $soDataItem->sku_sap_code,
                        'customer_sku_name' => $soDataItem->sku_sap_name,
                        'customer_sku_unit' => $soDataItem->sku_sap_unit,
                        'quantity2_po' => $soDataItem->quantity3_sap,
                    ];
                });
            }

            foreach ($itemsArray as $item) {
                // Nếu $customer_group_id = 11 (Đơn bổ sung) thì giữ lại đúng item
                if ($customer_group_id == 11) {
                    $item['sku_sap_code'] = $item['customer_sku_code'];
                    $item['sku_sap_name'] = $item['customer_sku_name'];
                    // Nếu sku_sap_unit trống thì mặc định là PC
                    $item['sku_sap_unit'] = !empty($item['customer_sku_unit']) ? $item['customer_sku_unit'] : "PC";
                    $item['quantity3_sap'] = $item['quantity2_po'];
                    array_push($totalMTSoItems[$so_uid], $item);
                    continue;
                }
                $customer_sku_code = $item['customer_sku_code'];
                $sapMaterial = null;
                $sapMaterialMapping = null;

                foreach ($sapMaterials as $material) {
                    if ($material['bar_code'] == $customer_sku_code) {
                        $sapMaterial = $material;
                        break;
                    }
                }
                if ($sapMaterial) {
                    $item['sku_sap_code'] = $sapMaterial['sap_code'];
                    $item['sku_sap_name'] = $sapMaterial['name'];
                    $item['sku_sap_unit'] = SapUnit::find($sapMaterial['unit_id'])->unit_code ?: null;
                    $item['quantity3_sap'] = $item['quantity2_po'];
                    array_push($totalMTSoItems[$so_uid], $item);
                } else {
                    $customerMaterial = null;
                    $matchedMappings = array();
                    foreach ($customerMaterials as $customer_material) {
                        if (($customer_material['customer_group_id'] == $customer_group_id) &&
                            ($customer_material['customer_sku_code'] == $customer_sku_code)) {
                            $customerMaterial = $customer_material;
                            break;
                        }
                    }

                    if ($customerMaterial) {
                        $customer_material_id  = $customerMaterial['id'];
                        $matchedMappings = array_filter($sapMaterialMappings, function ($mapping) use ($customer_material_id) {
                            return $mapping['customer_material_id'] == $customer_material_id;
                        });
                    }
                    if ($matchedMappings) {
                        $temp_item = $item;
                        foreach ($matchedMappings as $matchedMapping) {
                            $customer_number = $matchedMapping['customer_number'];
                            if ($customer_number != 0) {
                                $existSapMaterial = null;
                                $sapMaterialId = $matchedMapping['sap_material_id'];
                                foreach ($sapMaterials as $material) {
                                    if ($material['id'] === $sapMaterialId) {
                                        $existSapMaterial = $material;
                                        break;
                                    }
                                }
                                if ($existSapMaterial) {
                                    $conversion_rate_sap = $matchedMapping['conversion_rate_sap'];
                                    $customer_number = $matchedMapping['customer_number'];
                                    $percentage = $matchedMapping['percentage'];
                                    $quantity2_po = $item['quantity2_po'];
                                    $quantity3_sap = (($quantity2_po * $conversion_rate_sap) / $customer_number) * ($percentage / 100);
                                    $temp_item['sku_sap_code'] = $existSapMaterial['sap_code'];
                                    $temp_item['sku_sap_name'] = $existSapMaterial['name'];
                                    $temp_item['sku_sap_unit'] = SapUnit::find($existSapMaterial['unit_id'])->unit_code ?: null;
                                    $temp_item['quantity3_sap'] = $quantity3_sap;
                                    array_push($totalMTSoItems[$so_uid], $temp_item);
                                }
                            }
                        }
                    } else {
                        $item['sku_sap_code'] = null;
                        $item['sku_sap_name'] = null;
                        $item['sku_sap_unit'] = null;
                        $item['quantity3_sap'] = $item['quantity2_po'];
                        array_push($totalMTSoItems[$so_uid], $item);
                    }
                }
            }
        }

        // Đếm số lượng phần con trong các mảng của totalMTSoItems
        $totalMTItemCount = 0;
        foreach ($totalMTSoItems as $so_uid => $items) {
            $totalMTItemCount += count($items);
        }
        Log::info("Số lượng item trên WEB: " . $totalMTItemCount . " items");
        // Count số $soHeaders
        $countSoHeaders = count($soHeaders);
        Log::info("Số lượng SO trên WEB: " . $countSoHeaders . " đơn hàng");

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy totalMTSoItems: " . $executionTime . " giây");
        $startTime = microtime(true);
        // Lấy data từ SAP
        $sapSoHeaders = [];
        foreach ($soHeaders as $soHeader) {
            $sapSoHeaders[] = ["VBELN" => $soHeader->so_uid];
        }
        $sapData = [
            "ID" => "1001",
            "action_name" => "FETCH_SALESORDERS",
            "BODY" => $sapSoHeaders
        ];
        $totalSAPSoItems = array();
        $totalSAPSoItemCount = 0;
        $json = SapApiHelper::postData(json_encode($sapData));
        $jsonData = json_decode(json_encode($json), true);
        if (!$jsonData['success']) {
            $this->errors['sap_error'] = $jsonData['error'];
            return null; // Trả về null nếu có lỗi
        } else {
            if (!empty($jsonData['data'])) {
                foreach ($jsonData['data'] as $json_value) {
                    // dd($jsonData['data']);
                    $so_numbers = $json_value['SO_NUMBER'];
                    $totalSAPSoItems[$so_numbers] = array();
                    $items = $json_value['ITEMS'] ?? [];
                    foreach ($items as $item) {
                        array_push($totalSAPSoItems[$so_numbers],$item);
                        $totalSAPSoItemCount++;
                    }
                }
            } else {
                return null;
            }

        }
        Log::info("Số lượng item trên SAP: " . $totalSAPSoItemCount . " items");
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi lấy data SAP: " . $executionTime . " giây");
        $startTime = microtime(true);
        $matchingItemsCount = 0;
        $onlyInMTCount = 0;
        $onlyInSAPCount = 0;
        foreach ($soHeaders as $soHeader) {
            $so_uid = $soHeader->so_uid;
            $soItemsMT = $totalMTSoItems[$so_uid] ?? [];
            $soItemsSAP = $totalSAPSoItems[$so_uid] ?? [];

            // Duyệt mảng $soItemsMT, gộp các item giống nhau và cộng số lượng dựa trên sku_sap_code và sku_sap_unit
            $mergedMTItems = [];
            foreach ($soItemsMT as $item) {
                $sku_sap_code = $item['sku_sap_code'];
                $sku_sap_unit = $item['sku_sap_unit'];

                // Tạo khóa duy nhất cho mỗi phần tử dựa trên sku_sap_code và sku_sap_unit
                $key = $sku_sap_code . '_' . $sku_sap_unit;

                // Kiểm tra xem phần tử đã tồn tại trong mảng tạm chưa
                if (isset($mergedMTItems[$key])) {
                    // Nếu tồn tại, cộng quantity3_sap của phần tử hiện tại vào phần tử đã tồn tại
                    $mergedMTItems[$key]['quantity3_sap'] += $item['quantity3_sap'];
                } else {
                    // Nếu chưa tồn tại, thêm phần tử vào mảng tạm
                    $mergedMTItems[$key] = $item;
                }
            }
            $soItemsMT = array_values($mergedMTItems);

            // Duyệt mảng $soItemsSAP, gộp các item giống nhau và cộng số lượng SAP_QUANTITY dựa trên SAP_CODE và UNIT_CODE
            $mergedSAPItems = [];
            foreach ($soItemsSAP as $item) {
                $sap_code = $item['SAP_CODE'];
                $unit_code = $item['UNIT_CODE'];
                $key = $sap_code . '_' . $unit_code;

                if (isset($mergedSAPItems[$key])) {
                    $mergedSAPItems[$key]['SAP_QUANTITY'] += $item['SAP_QUANTITY'];
                } else {
                    $mergedSAPItems[$key] = $item;
                }
            }
            $soItemsSAP = array_values($mergedSAPItems);

            $userSAPDefault = null;
            $matchingItems = [];
            $onlyInMT = [];
            $onlyInSAP = [];

            // Đưa `$soItemsSAP` vào một mảng với khóa là "SAP_CODE_UNIT_CODE" để dễ tra cứu
            $sapIndex = [];
            foreach ($soItemsSAP as $itemSAP) {
                $key = $itemSAP['SAP_CODE'] . '_' . $itemSAP['UNIT_CODE'];
                $key = strtolower($key);
                $sapIndex[$key] = $itemSAP;
                $userSAPDefault = $itemSAP['USER']?? null;
            }

            // Kiểm tra các item trong `$soItemsMT`
            foreach ($soItemsMT as $itemMT) {
                $key = $itemMT['sku_sap_code'] . '_' . $itemMT['sku_sap_unit'];
                $key = strtolower($key);

                if (isset($sapIndex[$key])) {
                    // Nếu trùng thì đưa vào danh sách matchingItems
                    $matchingItems[] = $itemMT;
                } else {
                    // Nếu không trùng thì đưa vào danh sách onlyInMT
                    $onlyInMT[] = $itemMT;
                }
            }

            // Tìm các item chỉ có trong `$soItemsSAP`
            // Tạo một mảng chỉ mục từ $matchingItems để truy cập nhanh hơn
            $matchingIndex = [];
            foreach ($matchingItems as $matching) {
                $matchingKey = strtolower($matching['sku_sap_code'] . '_' . $matching['sku_sap_unit']);
                $matchingIndex[$matchingKey] = true;
            }

            foreach ($soItemsSAP as $itemSAP) {
                $key = strtolower($itemSAP['SAP_CODE'] . '_' . $itemSAP['UNIT_CODE']);
                // Kiểm tra sự tồn tại của $key trong mảng chỉ mục
                if (!isset($matchingIndex[$key])) {
                    $onlyInSAP[] = $itemSAP;
                }
            }

            $matchingItemsCount += count($matchingItems);
            $onlyInMTCount += count($onlyInMT);
            $onlyInSAPCount += count($onlyInSAP);

            // Tồn tại cả 2 bảng
            foreach ($matchingItems as $matchingItem) {
                $item = array();
                $key = $matchingItem['sku_sap_code'] . '_' . $matchingItem['sku_sap_unit'];
                $key = strtolower($key);

                $sapItem = $sapIndex[$key];
                $sapQuantity = $sapItem['SAP_QUANTITY'] ?? null;
                $quantity3_sap = $matchingItem['quantity3_sap'] ?? null;

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
                $item['sap_code'] = $sapItem['SAP_CODE'] ?? null;
                $item['sap_name'] = $sapItem['SAP_NAME'] ?? null;
                $item['sap_quantity'] = $sapQuantity;
                $item['sap_unit_code'] = $sapItem['UNIT_CODE']?? null;
                $item['sap_user'] = $sapItem['USER']?? null;
                $item['fulfillment_rate'] = $fulfillmentRate;

                // $updatedItem = new \stdClass(); // Tạo một đối tượng mới cho mỗi item
                $item['id'] = $soHeader->id;
                $item['customer_code'] = $soHeader->customer_code;
                $item['customer_name'] = $soHeader->customer_name;
                $item['po_number'] = $soHeader->po_number;
                $item['so_uid'] = $soHeader->so_uid;
                $item['created_at'] = $soHeader->created_at;
                $item['order_process'] = array();
                $item['order_process']['id'] = $soHeader->order_process->id;
                $item['order_process']['customer_group_name'] = $soHeader->order_process->customer_group->name;
                $item['order_process']['created_by'] = $soHeader->order_process->created_by;

                $item['raw_po_header_id'] = $matchingItem['raw_po_header_id'] ?? null;
                $item['customer_sku_code'] = $matchingItem['customer_sku_code'] ?? null;
                $item['customer_sku_name'] = $matchingItem['customer_sku_name'] ?? null;
                $item['customer_sku_unit'] = $matchingItem['customer_sku_unit'] ?? null;
                $item['sku_sap_code'] = $matchingItem['sku_sap_code'] ?? null;
                $item['sku_sap_name'] = $matchingItem['sku_sap_name'] ?? null;
                $item['sku_sap_unit'] = $matchingItem['sku_sap_unit'] ?? null;
                $item['quantity3_sap'] = $quantity3_sap;

                $result->so_items[] = (object) $item;
            }

            // Chỉ tồn tại bảng WEB
            foreach ($onlyInMT as $itemMT) {
                $item = array();

                $sapQuantity = null;
                $quantity3_sap = $itemMT['quantity3_sap'] ?? null;

                $fulfillmentRate = 0;
                // Cập nhật các giá trị khác
                $item['sap_code'] = null;
                $item['sap_name'] = null;
                $item['sap_quantity'] = $sapQuantity;
                $item['sap_unit_code'] = null;
                $item['sap_user'] = $userSAPDefault;
                $item['fulfillment_rate'] = $fulfillmentRate;

                $item['id'] = $soHeader->id;
                $item['customer_code'] = $soHeader->customer_code;
                $item['customer_name'] = $soHeader->customer_name;
                $item['po_number'] = $soHeader->po_number;
                $item['so_uid'] = $soHeader->so_uid;
                $item['created_at'] = $soHeader->created_at;
                $item['order_process'] = array();
                $item['order_process']['id'] = $soHeader->order_process->id;
                $item['order_process']['customer_group_name'] = $soHeader->order_process->customer_group->name;
                $item['order_process']['created_by'] = $soHeader->order_process->created_by;

                $item['raw_po_header_id'] = $itemMT['raw_po_header_id'] ?? null;
                $item['customer_sku_code'] = $itemMT['customer_sku_code'] ?? null;
                $item['customer_sku_name'] = $itemMT['customer_sku_name'] ?? null;
                $item['customer_sku_unit'] = $itemMT['customer_sku_unit'] ?? null;
                $item['sku_sap_code'] = $itemMT['sku_sap_code'] ?? null;
                $item['sku_sap_name'] = $itemMT['sku_sap_name'] ?? null;
                $item['sku_sap_unit'] = $itemMT['sku_sap_unit'] ?? null;
                $item['quantity3_sap'] = $quantity3_sap;

                $result->so_items[] = (object) $item;
            }

            // Chỉ tồn tại ở bảng SAP
            foreach ($onlyInSAP as $itemSAP) {
                $item = array();

                $sapQuantity = $itemSAP['SAP_QUANTITY'] ?? null;
                $quantity3_sap = null;

                $fulfillmentRate = 100;
                // Cập nhật các giá trị khác
                $item['sap_code'] = $itemSAP['SAP_CODE'] ?? null;
                $item['sap_name'] = $itemSAP['SAP_NAME'] ?? null;
                $item['sap_quantity'] = $sapQuantity;
                $item['sap_unit_code'] = $itemSAP['UNIT_CODE']?? null;
                $item['sap_user'] = $itemSAP['USER']?? null;
                $item['fulfillment_rate'] = $fulfillmentRate;

                $item['id'] = $soHeader->id;
                $item['customer_code'] = $soHeader->customer_code;
                $item['customer_name'] = $soHeader->customer_name;
                $item['po_number'] = $soHeader->po_number;
                $item['so_uid'] = $soHeader->so_uid;
                $item['created_at'] = $soHeader->created_at;
                $item['order_process'] = array();
                $item['order_process']['id'] = $soHeader->order_process->id;
                $item['order_process']['customer_group_name'] = $soHeader->order_process->customer_group->name;
                $item['order_process']['created_by'] = $soHeader->order_process->created_by;

                $item['raw_po_header_id'] = null;
                $item['customer_sku_code'] = null;
                $item['customer_sku_name'] = null;
                $item['customer_sku_unit'] = null;
                $item['sku_sap_code'] = null;
                $item['sku_sap_name'] = null;
                $item['sku_sap_unit'] = null;
                $item['quantity3_sap'] = $quantity3_sap;

                $result->so_items[] = (object) $item;
            }
        }
        if ($this->request->filled('sap_code')) {
            $sap_code = $this->request->sap_code;
            $result->so_items = array_filter($result->so_items, function ($item) use ($sap_code) {
                return $item->sap_code == $sap_code;
            });
        }
        if ($this->request->filled('sap_user')) {
            $sap_user = $this->request->sap_user;
            $result->so_items = array_filter($result->so_items, function ($item) use ($sap_user) {
                return $item->sap_user == $sap_user;
            });
        }

        Log::info("Số lượng item có cả 2 bảng: " . $matchingItemsCount. " items");
        Log::info("Số lượng item chỉ có ở bảng WEB: " . $onlyInMTCount . " items");
        Log::info("Số lượng item chỉ có ở bảng SAP: " . $onlyInSAPCount. " items");

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = round($executionTime, 2);
        Log::info("Thời gian thực thi mapping data: " . $executionTime . " giây");
        // Lấy trang hiện tại từ request (mặc định là trang 1)
        $perPage = $this->request->get('per_page', 10);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Tổng số phần tử
        $total = count($result->so_items);

        // Lấy dữ liệu cho trang hiện tại
        $currentPageItems = array_slice($result->so_items, ($currentPage - 1) * $perPage, $perPage);

        // Tạo đối tượng LengthAwarePaginator
        $paginatedResult = new LengthAwarePaginator(
            $currentPageItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $paginatedResult;
    }
}
