<?php

namespace App\Repositories\Business;

use App\Enums\OrderStatus;
use App\Models\Business\Order;
use App\Models\Business\OrderCustomerReviewCriteria;
use App\Models\Master\OrderReviewOption;
use App\Repositories\Abstracts\RepositoryAbs;
use Carbon\Carbon;

class DashboardRepository extends RepositoryAbs
{
    public function getDashboardStatistic()
    {
        try {
            $delivery_partner_ids = $this->request->filled('delivery_partner_ids') ? $this->request->delivery_partner_ids : [];

            $query = Order::query();
            // Lọc theo danh sách khách hàng (nếu có)
            if ($this->request->filled('customer_ids')) $query->whereIn('customer_id', $this->request->customer_ids);
            // Lọc theo danh sách đối tác giao hàng (nếu có)
            if ($this->request->filled('delivery_partner_ids') || $this->request->filled('month_year')) {
                $query->whereHas('deliveries', function ($query) use ($delivery_partner_ids) {
                    // Lọc theo danh sách đối tác giao hàng (nếu có)
                    if ($this->request->filled('delivery_partner_ids'))  $query->whereIn('delivery_partner_id', $delivery_partner_ids);
                });
                $month_year = $this->request->month_year;
                $query->whereHas('approved', function ($query) use ($month_year) {
                    // Lọc theo tháng năm (nếu có)
                    list($month, $year) = explode('-', $month_year);
                    $query->whereMonth('sap_so_finance_approval_date', $month)->whereYear('sap_so_finance_approval_date', $year);
                });
            }

            // $lateOrders = $query->where('status', 'late')->count();
            // $totalDeliveredOrders = $query->where('status', 'delivered')->count();
            // $unconfirmedOrders = $query->where('status', 'delivered')->whereNull('confirmed_at')->count();
            // $unevaluatedOrders = $query->where('status', 'received')->whereNull('evaluation_id')->count();

            $delivering_orders_count = $query->where('status_id', OrderStatus::Delivering)->count();
            $late_orders_count = $query->where('status_id', '>=', OrderStatus::Preparing)
                ->where('status_id', '<', OrderStatus::Delivered)
                ->whereHas('deliveries', function ($query) {
                    $query->where('estimate_delivery_date', '<', Carbon::today());
                })->count();
            $ontime_orders_count = $query->where('status_id', '>=', OrderStatus::Delivered)
                ->whereHas('deliveries', function ($query) {
                    $query->where('estimate_delivery_date', '>=', 'confirm_delivery_date');
                })->count();
            $delivered_orders_count = $query->where('status_id', OrderStatus::Delivered)->count();
            $confirmed_orders_count = $query->where('status_id', OrderStatus::Delivered)
                ->whereHas('delivery_info', function ($query) {
                    $query->whereNotNull('confirm_delivery_date');
                })->count();
            $received_orders_count = $query->where('status_id', OrderStatus::Received)->count();
            $reviewed_orders_count = $query->where('status_id', OrderStatus::Received)
                ->whereHas('customer_reviews')->count();

            $data = array(
                'delivering_orders_count' => $delivering_orders_count,
                'late_orders_count' => $late_orders_count,
                'ontime_orders_count' => $ontime_orders_count,
                'delivered_orders_count' => $delivered_orders_count,
                'confirmed_orders_count' => $confirmed_orders_count,
                'received_orders_count' => $received_orders_count,
                'reviewed_orders_count' => $reviewed_orders_count,
            );
            return $data;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getCriteriaStatistic()
    {
        try {
            $delivery_partner_ids = $this->request->filled('delivery_partner_ids') ? $this->request->delivery_partner_ids : [];
            $customer_ids = $this->request->filled('customer_ids') ? $this->request->customer_ids : [];
            $month_year = $this->request->filled('month_year') ? $this->request->month_year : Carbon::now()->format('m-Y');
            list($month, $year) = explode('-', $month_year);

            $query = OrderReviewOption::query()
                ->select('order_review_options.id', 'order_review_options.name')
                ->selectRaw('COUNT(order_customer_reviews.order_id) as amount')
                ->join('order_customer_review_criterias', 'order_review_options.id', '=', 'order_customer_review_criterias.criteria_id')
                ->join('order_customer_reviews', 'order_customer_reviews.id', '=', 'order_customer_review_criterias.review_id')
                ->join('orders', 'orders.id', '=', 'order_customer_reviews.order_id')
                ->groupBy('order_review_options.id', 'order_review_options.name');

            // Apply filters
            if ($this->request->filled('customer_ids')) {
                $query->whereIn('orders.customer_id', $customer_ids);
            }
            if ($this->request->filled('delivery_partner_ids') || $this->request->filled('month_year')) {
                $query->whereHas('orders.deliveries', function ($query) use ($delivery_partner_ids) {
                    if ($this->request->filled('delivery_partner_ids')) {
                        $query->whereIn('delivery_partner_id', $delivery_partner_ids);
                    }
                });
                $query->whereHas('orders.approved', function ($query) use ($month, $year) {
                    $query->whereMonth('sap_so_finance_approval_date', $month)->whereYear('sap_so_finance_approval_date', $year);
                });
            }

            return $query->orderByDesc('id')->get();
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
