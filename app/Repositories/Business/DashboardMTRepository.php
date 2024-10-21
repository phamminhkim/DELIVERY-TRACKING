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
    //phương thức xử lý bộ lọc chung
    protected function applyFilters($query)
    {
        // Xử lý from_date và to_date
        if ($this->request->filled('from_date') || $this->request->filled('to_date')) {
            if ($this->request->filled('from_date')) {
                $from_date = $this->request->from_date;
                $query->whereDate('so_headers.created_at', '>=', $from_date);
            }
            if ($this->request->filled('to_date')) {
                $to_date = $this->request->to_date;
                $query->whereDate('so_headers.created_at', '<=', $to_date);
            }
        } else {
            // Mặc định lấy dữ liệu trong 1 tháng gần nhất
            $query->whereDate('so_headers.created_at', '>=', now()->subMonth()->toDateString());
        }

        // Các bộ lọc khác
        if ($this->request->filled('so_uid')) {
            $so_uid = $this->request->so_uid;
            $query->where('so_uid', 'LIKE', '%' . $so_uid . '%');
        }

        if ($this->request->filled('order_process_id')) {
            $order_process_ids = $this->request->order_process_id;
            $query->whereIn('order_process_id', $order_process_ids);
        }

        if ($this->request->filled('po_number')) {
            $po_number = $this->request->po_number;
            $query->where('po_number', 'LIKE', '%' . $po_number . '%');
        }

        if ($this->request->filled('customer_name')) {
            $customer_name = $this->request->customer_name;
            $query->where('customer_name', 'LIKE', '%' . $customer_name . '%');
        }

        if ($this->request->filled('customer_code')) {
            $customer_code = $this->request->customer_code;
            $query->where('customer_code', 'LIKE', '%' . $customer_code . '%');
        }
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

        if ($this->request->filled('user_id')) {
            $user_id = $this->request->user_id;
            $query->whereHas('order_process', function ($query) use ($user_id) {
                $query->where('created_by', $user_id);
            });
        }

        return $query;
    }

    public function getPoByUser()
    {
        try {
            // Kiểm tra quyền người dùng
            if (!$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }

            // Query cơ bản từ bảng SoHeader
            $query = SoHeader::query();

            // Gọi hàm xử lý bộ lọc
            $this->applyFilters($query);

            // Sử dụng join với bảng order_process và loại bỏ các bản ghi có is_deleted = 1
            $query->join('order_processes', function ($join) {
                $join->on('so_headers.order_process_id', '=', 'order_processes.id')
                    ->where('order_processes.is_deleted', '=', 0); // Thêm điều kiện is_deleted = 0
            })
                // Thêm join với bảng users để lấy thông tin tên của created_by
                ->join('users', 'order_processes.created_by', '=', 'users.id')
                ->select('order_processes.created_by as id', 'users.name', DB::raw('COUNT(so_headers.id) as total_orders'))
                ->groupBy('order_processes.created_by', 'users.name');

            // Truy vấn dữ liệu
            $result = $query->get();
            // Định dạng lại kết quả trả về như mong muốn
            $data = $result->map(function ($item) {
                return [
                    'created_by' => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ],
                    'total_orders' => $item->total_orders,
                ];
            });

            // Trả về dữ liệu đã được định dạng
            return $data;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getPoByCustomerGroup()
    {
        try {
            // Kiểm tra quyền người dùng
            if (!$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }

            // Query cơ bản từ bảng SoHeader
            $query = SoHeader::query();

            // Gọi hàm xử lý bộ lọc
            $this->applyFilters($query);

            // Sử dụng join với bảng order_process và loại bỏ các bản ghi có is_deleted = 1
            $query->join('order_processes', function ($join) {
                $join->on('so_headers.order_process_id', '=', 'order_processes.id')
                    ->where('order_processes.is_deleted', '=', 0); // Thêm điều kiện is_deleted = 0
            })
                // Thêm join với bảng customer_groups để lấy tên của customer_group
                ->join('customer_groups', 'order_processes.customer_group_id', '=', 'customer_groups.id')
                ->select('order_processes.customer_group_id as id', 'customer_groups.name', DB::raw('COUNT(so_headers.id) as total_orders'))
                ->groupBy('order_processes.customer_group_id', 'customer_groups.name');

            // Truy vấn dữ liệu
            $result = $query->get();

            // Định dạng lại kết quả trả về như mong muốn
            $data = $result->map(function ($item) {
                return [
                    'customer_group' => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ],
                    'total_orders' => $item->total_orders,
                ];
            });

            // Trả về dữ liệu đã được định dạng
            return $data;
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

            // Query cơ bản từ bảng SoHeader
            $query = SoHeader::query();

            // Gọi hàm xử lý bộ lọc
            $this->applyFilters($query);

            // Thêm điều kiện join với bảng order_processes và lấy thêm sync_sap_status từ bảng so_headers
            $query->join('order_processes', 'so_headers.order_process_id', '=', 'order_processes.id')
                ->selectRaw('so_headers.sync_sap_status, COUNT(so_headers.id) as total_orders')
                ->groupBy('so_headers.sync_sap_status');

            // Truy vấn dữ liệu
            $soHeader = $query->get();

            // Định dạng kết quả
            $data = $soHeader->map(function ($item) {
                return [
                    'sync_sap_status' => $item->sync_sap_status,
                    'total_orders' => $item->total_orders,
                ];
            });

            // Tính tổng số đơn hàng của tất cả các trạng thái
            // $grandTotal = $soHeader->sum('total_orders');

            // // Trả về dữ liệu đã được định dạng kèm tổng số đơn hàng
            // return [
            //     'sync_sap' => $data,
            //     'sync_total' => $grandTotal,
            // ];
            return $data;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getPoByDate()
    {
        try {
            // Kiểm tra quyền người dùng
            if (!$this->current_user->hasRole(['admin-system'])) {
                return collect([]);
            }
            // Query cơ bản từ bảng SoHeader
            $query = SoHeader::query();

            // Gọi hàm xử lý bộ lọc (nếu có)
            $this->applyFilters($query);

            // Thêm điều kiện lấy tổng số đơn hàng theo ngày
            $query->selectRaw('DATE(so_headers.created_at) as order_date, COUNT(so_headers.id) as total_orders')
                ->groupBy('order_date');

            // Truy vấn dữ liệu
            $soHeader = $query->get();
            $data = $soHeader->map(function ($item) {
                return [
                    'order_date' => $item->order_date,
                    'total_orders' => $item->total_orders,
                ];
            });
            return $data;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
