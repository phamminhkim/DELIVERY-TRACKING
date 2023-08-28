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
            $month_year = $this->request->month_year;
            list($month, $year) = explode('-', $month_year);

            $query = Order::query();
            // Lọc theo danh sách khách hàng (nếu có)
            if ($this->request->filled('customer_ids')) $query->whereIn('customer_id', $this->request->customer_ids);
            // Lọc theo danh sách đối tác giao hàng (nếu có)
            if ($this->request->filled('delivery_partner_ids')) {
                $query->whereHas('deliveries', function ($query) use ($delivery_partner_ids) {
                    // Lọc theo danh sách đối tác giao hàng (nếu có)
                    if ($this->request->filled('delivery_partner_ids'))  $query->whereIn('delivery_partner_id', $delivery_partner_ids);
                });
            }

            $this_month_query = clone $query;
            $this_month_query->whereHas('approved', function ($query) use ($month, $year) {
                // Lọc theo tháng năm (nếu có)
                $query->whereMonth('sap_so_finance_approval_date', $month)->whereYear('sap_so_finance_approval_date', $year);
            });

            $last_month_query = clone $query;
            $last_month_query->whereHas('approved', function ($query) use ($month, $year) {
                $last_month = $month - 1;
                if (
                    $last_month == 0
                ) {
                    $last_month = 12;
                    $last_year = $year - 1;
                } else {
                    $last_year = $year;
                }
                $query->whereMonth('sap_so_finance_approval_date', $last_month)->whereYear('sap_so_finance_approval_date', $last_year);
            });

            $delivering_orders_count_query = clone $this_month_query;
            $delivering_orders_count = $delivering_orders_count_query->where('status_id', '=', OrderStatus::Delivering)->count();

            $late_orders_count_query = clone $this_month_query;
            $late_orders_count = $late_orders_count_query->where('status_id', '>=', OrderStatus::Preparing)
                ->where('status_id', '<', OrderStatus::Delivered)
                ->whereHas('deliveries', function ($late_orders_count_query) {
                    $late_orders_count_query->whereDate('estimate_delivery_date', '<', Carbon::today());
                })->count();

            $ontime_orders_count_query = clone $this_month_query;
            $ontime_orders_count = $ontime_orders_count_query->where('status_id', '>=', OrderStatus::Delivered)
                ->whereHas('deliveries', function ($ontime_orders_count_query) {
                    $ontime_orders_count_query->whereDate('estimate_delivery_date', '>=', 'confirm_delivery_date');
                })
                ->count();

            $delivered_orders_count_query = clone $this_month_query;
            $delivered_orders_count = $delivered_orders_count_query
                ->where('status_id', '>=', OrderStatus::Delivered)
                ->count();

            // $confirmed_orders_count_query = clone $this_month_query;
            // $confirmed_orders_count = $confirmed_orders_count_query->where('status_id', OrderStatus::Delivered)
            //     ->whereHas('delivery_info', function ($confirmed_orders_count_query) {
            //         $confirmed_orders_count_query->whereNotNull('confirm_delivery_date');
            //     })->count();

            $received_orders_count_query = clone $this_month_query;
            $received_orders_count = $received_orders_count_query->where('status_id', OrderStatus::Received)->count();

            $reviewed_orders_count_query = clone $this_month_query;
            $reviewed_orders_count = $reviewed_orders_count_query->where('status_id', OrderStatus::Received)
                ->whereHas('customer_reviews')->count();

            // Last month's data
            $late_orders_count_last_month_query = clone $last_month_query;
            $late_orders_count_last_month = $late_orders_count_last_month_query->where('status_id', '>=', OrderStatus::Preparing)
                ->where('status_id', '<', OrderStatus::Delivered)
                ->whereHas('deliveries', function ($late_orders_count_last_month_query) {
                    $late_orders_count_last_month_query->whereDate('estimate_delivery_date', '<', Carbon::today());
                })->count();

            $ontime_orders_count_last_month_query = clone $last_month_query;
            $ontime_orders_count_last_month = $ontime_orders_count_last_month_query->where('status_id', '>=', OrderStatus::Delivered)
                ->whereHas('deliveries', function ($ontime_orders_count_last_month_query) {
                    $ontime_orders_count_last_month_query->whereDate('estimate_delivery_date', '>=', 'confirm_delivery_date');
                })->count();

            // Calculate percentage changes
            $late_orders_percentage_change = ($late_orders_count_last_month != 0) ? (($late_orders_count - $late_orders_count_last_month) / $late_orders_count_last_month) * 100 : 0;
            $ontime_orders_percentage_change = ($ontime_orders_count_last_month != 0) ? (($ontime_orders_count - $ontime_orders_count_last_month) / $ontime_orders_count_last_month) * 100 : 0;


            $data = array(
                'delivering_orders_count' => $delivering_orders_count,
                'late_orders_count' => $late_orders_count,
                'ontime_orders_count' => $ontime_orders_count,
                'delivered_orders_count' => $delivered_orders_count,
                // 'confirmed_orders_count' => $confirmed_orders_count,
                'received_orders_count' => $received_orders_count,
                'reviewed_orders_count' => $reviewed_orders_count,
                'late_orders_percentage_change' => $late_orders_percentage_change,
                'ontime_orders_percentage_change' => $ontime_orders_percentage_change,
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
                ->join('order_approveds', 'order_approveds.order_id', '=', 'orders.id')
                ->leftJoin('order_deliveries', 'orders.id', '=', 'order_deliveries.order_id')
                ->leftJoin('deliveries', 'deliveries.id', '=', 'order_deliveries.delivery_id');
            if ($this->request->filled('customer_ids')) {
                $query->whereIn('orders.customer_id', $customer_ids);
            }
            if ($this->request->filled('delivery_partner_ids')) {
                $query->whereIn('deliveries.delivery_partner_id', $delivery_partner_ids);
            }
            if ($this->request->filled('month_year')) {
                $query->whereMonth('order_approveds.sap_so_finance_approval_date', $month)
                    ->whereYear('order_approveds.sap_so_finance_approval_date', $year);
            }
            $query->groupBy('order_review_options.id', 'order_review_options.name');
            $query->orderBy('order_review_options.id', 'desc');
            return $query->get();
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
