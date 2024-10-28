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

            // Lấy danh sách user_ids từ yêu cầu (sử dụng null nếu không có)
            $user_ids = $this->request->filled('user_ids') ? $this->request->user_ids : null;

            // Khởi tạo truy vấn lấy danh sách người dùng và các thông tin liên quan đến đơn hàng
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

    // public function getPoBySyncStatus()
    // {
    //     try {
    //         // Kiểm tra quyền người dùng
    //         if (!$this->current_user->hasRole(['admin-system'])) {
    //             return collect([]);
    //         }

    //         // Khởi tạo biến ngày mặc định là từ đầu đến cuối tháng hiện tại
    //         $startDate = now()->startOfMonth();
    //         $endDate = now()->endOfMonth();

    //         // Nếu có from_date và to_date thì ghi đè lên khoảng ngày mặc định
    //         if ($this->request->filled('from_date')) {
    //             $startDate = Carbon::parse($this->request->from_date)->startOfDay();
    //         }
    //         if ($this->request->filled('to_date')) {
    //             $endDate = Carbon::parse($this->request->to_date)->endOfDay();
    //         }

    //         // Khởi tạo truy vấn sử dụng LEFT JOIN để đảm bảo lấy tất cả người dùng
    //         $query = DB::table('order_processes')
    //             ->join('users', 'order_processes.created_by', '=', 'users.id')
    //             ->leftJoin('so_headers', function ($join) use ($startDate, $endDate) {
    //                 $join->on('order_processes.id', '=', 'so_headers.order_process_id')
    //                     ->whereBetween('so_headers.created_at', [$startDate, $endDate]);
    //             })
    //             ->select(
    //                 DB::raw('COUNT(so_headers.id) as total_orders'),
    //                 DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 1 THEN 1 ELSE 0 END) as synced_orders'),
    //                 DB::raw('SUM(CASE WHEN so_headers.sync_sap_status = 0 THEN 1 ELSE 0 END) as unsynced_orders')
    //             );

    //         // Lọc thêm nếu có các trường khác
    //         if ($this->request->filled('sync_sap_status')) {
    //             $sync_sap_status = $this->request->sync_sap_status;
    //             $query->where('so_headers.sync_sap_status', 'LIKE', '%' . $sync_sap_status . '%');
    //         }

    //         if ($this->request->filled('customer_group_ids')) {
    //             $customer_group_ids = $this->request->customer_group_ids;
    //             $query->where('order_processes.customer_group_id', $customer_group_ids);
    //         }

    //         if ($this->request->filled('user_ids')) {
    //             $user_ids = $this->request->user_ids;
    //             $query->where('order_processes.created_by', $user_ids);
    //         }

    //         // Thực thi truy vấn và lấy kết quả
    //         $result = $query->get();

    //         // Tạo các mảng chứa thông tin người dùng và tổng số đơn hàng của họ
    //         $syncedOrders = [];
    //         $unsynced_orders = [];

    //         foreach ($result as $item) {
    //             $syncedOrders[] = (int) $item->synced_orders; // Số đơn hàng đã đồng bộ
    //             $unsynced_orders[] = (int) $item->unsynced_orders; // Số đơn hàng chưa đồng bộ
    //         }

    //         // Trả về dữ liệu dưới dạng JSON
    //         return
    //             [
    //                 'synced_orders' => $syncedOrders, // Mảng chứa số đơn hàng đã đồng bộ
    //                 'unsynced_orders' => $unsynced_orders // Mảng chứa số đơn hàng chưa đồng bộ
    //             ];
    //     } catch (\Exception $exception) {
    //         $this->message = $exception->getMessage();
    //         $this->errors = $exception->getTrace();
    //     }
    // }
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
}
