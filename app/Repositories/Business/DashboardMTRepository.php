<?php

namespace App\Repositories\Business;

use App\Models\Business\PublicHoliday;
use App\Repositories\Abstracts\RepositoryAbs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Business\SoHeader;

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

            // Khởi tạo truy vấn sử dụng LEFT JOIN để đảm bảo lấy tất cả người dùng
            $query = DB::table('order_processes')
                ->join('users', 'order_processes.created_by', '=', 'users.id')
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
                ->where('order_processes.is_deleted', '=', 0)
                ->groupBy('users.id', 'users.name');

            // Lọc thêm nếu có các trường khác
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->where('so_headers.sync_sap_status', 'LIKE', '%' . $sync_sap_status . '%');
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->whereIn('order_processes.customer_group_id', $customer_group_ids);
            }

            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->where('order_processes.created_by', $user_ids);
            }

            // Thực thi truy vấn và lấy kết quả
            $result = $query->get();

            // Tạo các mảng chứa thông tin người dùng và tổng số đơn hàng của họ
            $users = [];
            $total_orders = [];
            $synced_orders = [];
            $unsynced_orders = [];

            foreach ($result as $item) {
                $users[] = $item->name; // Tên người dùng
                $total_orders[] = (int) $item->total_orders; // Tổng số đơn hàng
                $synced_orders[] = (int) $item->synced_orders; // Số đơn hàng đã đồng bộ
                $unsynced_orders[] = (int) $item->unsynced_orders; // Số đơn hàng chưa đồng bộ
            }

            // Trả về dữ liệu dưới dạng JSON
            return
                [
                    'users' => $users, // Mảng chứa tên người dùng
                    'total_orders' => $total_orders, // Mảng chứa tổng số đơn hàng
                    'synced_orders' => $synced_orders, // Mảng chứa số đơn hàng đã đồng bộ
                    'unsynced_orders' => $unsynced_orders // Mảng chứa số đơn hàng chưa đồng bộ
                ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return response()->json(['success' => false, 'message' => $this->message, 'errors' => $this->errors], 500);
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

            // Khởi tạo truy vấn sử dụng LEFT JOIN để đảm bảo lấy tất cả nhóm khách hàng
            $query = DB::table('customer_groups')
                ->leftJoin('order_processes', function ($join) {
                    $join->on('customer_groups.id', '=', 'order_processes.customer_group_id')
                        ->where('order_processes.is_deleted', '=', 0); // Thêm điều kiện để lấy các order_process không bị xóa
                })
                ->leftJoin('so_headers', function ($join) use ($startDate, $endDate) {
                    $join->on('order_processes.id', '=', 'so_headers.order_process_id')
                        ->where('so_headers.created_at', '>=', $startDate)
                        ->where('so_headers.created_at', '<=', $endDate);
                })
                ->select('customer_groups.name', DB::raw('COUNT(so_headers.id) as total_orders'))
                ->groupBy('customer_groups.id', 'customer_groups.name');


            // Lọc thêm nếu có các trường khác
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->where('so_headers.sync_sap_status', 'LIKE', '%' . $sync_sap_status . '%');
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->where('order_processes.customer_group_id', $customer_group_ids);
            }

            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->where('order_processes.created_by', $user_ids);
            }

            $soHeaders = $query->get();

            // Tạo hai mảng để chứa tên nhóm khách hàng và tổng số đơn hàng
            $group = [];
            $total = [];

            foreach ($soHeaders as $header) {
                $group[] = $header->name; // Tên của nhóm khách hàng
                $total[] = (int) $header->total_orders; // Tổng số đơn hàng (có thể là 0 nếu không có đơn hàng)
            }
            return [
                "group" => $group,
                "total" => $total
            ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return response()->json(['success' => false, 'message' => $this->message, 'errors' => $this->errors], 500);
        }
    }
    public function getPoByDate()
    {
        try {
            $user = auth()->user();

            // Kiểm tra quyền truy cập
            if (!$user || !$this->current_user->hasRole(['admin-system'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
            $query = SoHeader::query()
                ->join('order_processes', function ($join) {
                    $join->on('so_headers.order_process_id', '=', 'order_processes.id')
                        ->where('order_processes.is_deleted', 0);
                })
                ->join('users', 'order_processes.created_by', '=', 'users.id')
                ->select('so_headers.created_at', DB::raw('COUNT(so_headers.id) as total_orders'))
                ->groupBy('so_headers.created_at');

            // Áp dụng bộ lọc ngày đã xác định
            $query->whereDate('so_headers.created_at', '>=', $startDate)
                ->whereDate('so_headers.created_at', '<=', $endDate);

            // Các bộ lọc khác nếu có
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->where('sync_sap_status', 'LIKE', '%' . $sync_sap_status . '%');
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->whereHas('order_process', function ($query) use ($customer_group_ids) {
                    $query->where('customer_group_id', $customer_group_ids);
                });
            }

            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->whereHas('order_process', function ($query) use ($user_ids) {
                    $query->where('created_by', $user_ids);
                });
            }

            // Thực hiện truy vấn và lấy kết quả
            $soHeader = $query->get();

            // Tạo mảng ngày và tổng số đơn hàng
            $dateRange = collect(\Carbon\CarbonPeriod::create($startDate, $endDate));
            $dates = [];
            $total = [];

            foreach ($dateRange as $date) {
                // Lọc đơn hàng theo ngày cụ thể
                $ordersOnDate = $soHeader->filter(function ($header) use ($date) {
                    return $header->created_at &&
                        $header->created_at->greaterThanOrEqualTo($date->startOfDay()) &&
                        $header->created_at->lessThanOrEqualTo($date->endOfDay());
                });

                // Tính tổng đơn hàng trong ngày
                $totalOrders = $ordersOnDate->count();

                // Thêm ngày và tổng đơn hàng vào mảng
                $dates[] = $date->format('d/m/Y'); // Định dạng ngày
                $total[] = $totalOrders; // Tổng số đơn hàng trong ngày
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

    public function getPoBySyncStatus()
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
                ->select(
                    DB::raw('COUNT(so_headers.id) as total_orders'),
                    DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 1 THEN 1 ELSE 0 END) as synced_orders'),
                    DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 0 THEN 1 ELSE 0 END) as unsynced_orders')
                );

            // Lọc thêm nếu có các trường khác
            if ($this->request->filled('sync_sap_status')) {
                $sync_sap_status = $this->request->sync_sap_status;
                $query->where('so_headers.sync_sap_status', 'LIKE', '%' . $sync_sap_status . '%');
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->where('order_processes.customer_group_id', $customer_group_ids);
            }

            if ($this->request->filled('user_ids')) {
                $user_ids = $this->request->user_ids;
                $query->where('order_processes.created_by', $user_ids);
            }

            // Thực thi truy vấn và lấy kết quả
            $result = $query->get();

            // Tạo các mảng chứa thông tin người dùng và tổng số đơn hàng của họ
            $syncedOrders = [];
            $unsynced_orders = [];

            foreach ($result as $item) {
                $syncedOrders[] = (int) $item->synced_orders; // Số đơn hàng đã đồng bộ
                $unsynced_orders[] = (int) $item->unsynced_orders; // Số đơn hàng chưa đồng bộ
            }

            // Trả về dữ liệu dưới dạng JSON
            return
                [
                    'synced_orders' => $syncedOrders, // Mảng chứa số đơn hàng đã đồng bộ
                    'unsynced_orders' => $unsynced_orders // Mảng chứa số đơn hàng chưa đồng bộ
                ];
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
