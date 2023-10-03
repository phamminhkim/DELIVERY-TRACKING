<?php

namespace App\Repositories\Business;

use App\Models\Business\Delivery;
use App\Models\Business\Order;
use App\Models\Business\OrderCustomerReviewCriteria;
use App\Models\Business\PublicHoliday;
use App\Models\Master\OrderReviewOption;
use App\Repositories\Abstracts\RepositoryAbs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\OrderStatus as ModelOrderStatus;
use App\Enums\OrderStatus;


class DashboardRepository extends RepositoryAbs
{
    public function getDashboardStatistic()
    {
        try {
            if (!$this->current_user->hasRole(['admin-system', 'admin-warehouse', 'admin-partner'])) {
                return collect([]);
            }
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
            if ($this->current_user->hasRole('admin-partner')) {
                $partner_ids = $this->current_user->delivery_partners->pluck('id')->toArray();
                $query->whereHas('deliveries', function ($query) use ($partner_ids) {
                    $query->whereIn('id', $partner_ids);
                });
            }

            if ($this->request->filled('warehouse_ids')) {
                $query->whereIn('warehouse_id', $this->request->warehouse_ids);
                $query->whereHas('warehouse', function ($query) {
                    $query->where('is_active', true);
                });
            }

            if ($this->request->filled('distribution_channel_ids')) {
                $distribution_channel_ids = $this->request->distribution_channel_ids;
                $query->whereHas('sale', function ($query) use ($distribution_channel_ids) {
                    $query->whereIn('distribution_channel_id', $distribution_channel_ids);
                    // Thêm điều kiện lọc theo trạng thái hoạt động
                    $query->where('is_active', true);
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
            $late_orders_count = $late_orders_count_query->where('status_id', '=', OrderStatus::Delivering)
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

            $pending_orders_query = Order::query()->where('status_id', '=', OrderStatus::Pending);
            $pending_orders_count = $pending_orders_query->count();

            $pending_today_orders_query = clone $pending_orders_query;
            $pending_today_orders_query->whereHas('approved', function ($query) {
                $query->whereDate('sap_do_posting_date', '=', Carbon::today());
            });
            $pending_today_orders_count = $pending_today_orders_query->count();

            $preparing_orders_query = clone $this_month_query;
            $preparing_orders_count = $preparing_orders_query
                ->where('status_id', '=', OrderStatus::Preparing)
                ->count();



            $data = array(
                'delivering_orders_count' => $delivering_orders_count,
                'late_orders_count' => $late_orders_count,
                'ontime_orders_count' => $ontime_orders_count,
                'delivered_orders_count' => $delivered_orders_count,
                // 'confirmed_orders_count' => $confirmed_orders_count,
                'received_orders_count' => $received_orders_count,
                'reviewed_orders_count' => $reviewed_orders_count,
                // 'late_orders_percentage_change' => $late_orders_percentage_change,
                // 'ontime_orders_percentage_change' => $ontime_orders_percentage_change,
                'pending_today_orders_count' => $pending_today_orders_count,
                'pending_orders_count' => $pending_orders_count,
                'preparing_orders_count' => $preparing_orders_count,

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
                ->leftJoin('order_sales', 'order_sales.order_id', '=', 'orders.id')
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
            if ($this->request->filled('warehouse_ids')) {
                $query->whereIn('orders.warehouse_id', $this->request->warehouse_ids);
            }
            if ($this->request->filled('distribution_channel_ids')) {
                $query->whereIn('order_sales.distribution_channel_id', $this->request->distribution_channel_ids);
            }
            $query->groupBy('order_review_options.id', 'order_review_options.name');
            $query->orderBy('order_review_options.id', 'desc');

            $statistic = $query->get();
            $statisticed_criterias = $statistic->pluck('id')->toArray();

            $criterias = OrderReviewOption::all();

            $unstatisticed_criterias = array_diff($criterias->pluck('id')->toArray(), $statisticed_criterias);
            foreach ($unstatisticed_criterias as $criteria_id) {
                $statistic->push([
                    'id' => $criteria_id,
                    'name' => $criterias->find($criteria_id)->name,
                    'amount' => 0
                ]);
            }


            return $statistic;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getReportStatistic()
    {
        try {
            $query = Order::query()->where('sap_do_number', '!=', null);

            if ($this->request->filled('from_date')) {
                $from_date = $this->request->from_date;
                $query->whereHas('deliveries', function ($query) use ($from_date) {
                    $query->whereDate('deliveries.start_delivery_date', '>=', $from_date);
                });
            }
            if ($this->request->filled('to_date')) {
                $to_date = $this->request->to_date;
                $query->whereHas('deliveries', function ($query) use ($to_date) {
                    $query->whereDate('deliveries.start_delivery_date', '<=', $to_date);
                });
            }

            if ($this->request->filled('company_codes')) {
                $comapny_codes = $this->request->company_codes;
                $query->whereHas('company', function ($query) use ($comapny_codes) {
                    $query->whereIn('company_code', $comapny_codes);
                });
            }
            if ($this->request->filled('customer_ids')) {
                $customer_ids = $this->request->customer_ids;
                $query->whereHas('customer', function ($query) use ($customer_ids) {
                    $query->whereIn('customer_id', $customer_ids);
                });
            }
            if ($this->request->filled('delivery_partner_ids')) {
                $delivery_partner_ids = $this->request->delivery_partner_ids;
                $query->whereHas('deliveries', function ($query) use ($delivery_partner_ids) {
                    $query->whereIn('delivery_partner_id', $delivery_partner_ids);
                });
            }
            if ($this->request->filled('warehouse_ids')) {
                $warehouse_ids = $this->request->warehouse_ids;
                $query->whereIn('warehouse_id', $warehouse_ids);
            }

            if ($this->request->filled('distribution_channel_ids')) {
                $distribution_channel_ids = $this->request->distribution_channel_ids;
                $query->whereHas('sale', function ($query) use ($distribution_channel_ids) {
                    $query->whereIn('distribution_channel_id', $distribution_channel_ids);
                });
            }
            $query->with(['customer', 'detail', 'deliveries']);
            $orders = $query->get();
            $public_holidays = PublicHoliday::all();
            $orders->map(function ($order) use ($public_holidays) {
                $delivery = $order->deliveries->first();
                if (!$delivery) {
                    $order->duration = 0;
                    return $order;
                }
                $start_date = Carbon::parse($delivery->start_delivery_date)->setTimezone('Asia/Ho_Chi_Minh');
                if (!$delivery->estimate_delivery_date) {
                    $order->duration = 0;
                    return $order;
                }
                $estimate_date = Carbon::parse($delivery->estimate_delivery_date)->setTimezone('Asia/Ho_Chi_Minh');
                $duration = $estimate_date->diffInDays($start_date) + 1;
                //loop foreach between start_date and estimate_date
                $looped_duration = $duration;
                for ($i = 0; $i <= $looped_duration; $i++) {
                    $date = $start_date->copy()->addDays($i);
                    //check if date is public holiday
                    $is_holiday = false;
                    foreach ($public_holidays as $public_holiday) {
                        $start_holiday_date = Carbon::parse($public_holiday->start_holiday_date)->setTimezone('Asia/Ho_Chi_Minh');
                        $end_holiday_date = Carbon::parse($public_holiday->end_holiday_date)->setTimezone('Asia/Ho_Chi_Minh');
                        if ($date->between($start_holiday_date, $end_holiday_date)) {
                            $duration--;
                            $is_holiday = true;
                        }
                    }
                    if (!$is_holiday && $date->format('l') == 'Sunday') {
                        $duration--;
                    }
                }
                $order->duration = $duration;
                if ($delivery->complete_delivery_date) {
                    $delivery['status'] = OrderStatus::Delivered;
                } else if ($delivery->start_delivery_date) {
                    $delivery['status'] = OrderStatus::Delivering;

                    // Check if any order is delivered or partly delivered

                } else {
                    $delivery['status'] = OrderStatus::Preparing;
                }

                if ($delivery['status'] >= OrderStatus::Preparing && $delivery->estimate_delivery_date) {

                    if ($delivery['status'] >= OrderStatus::Delivered) {
                        if ($delivery->estimate_delivery_date->lt($delivery->complete_delivery_date)) {
                            $delivery['is_late_deadline'] = false;
                        } else {
                            $delivery['is_late_deadline'] = true;
                        }
                    } else {
                        if ($delivery->estimate_delivery_date->lt(Carbon::today())) {
                            $delivery['is_late_deadline'] = false;
                        } else {
                            $delivery['is_late_deadline'] = true;
                        }
                    }
                } else {
                    $delivery['is_late_deadline'] = false;
                }

                $delivery['status'] = ModelOrderStatus::find($delivery['status']);
                return $delivery;
            });

            return $orders;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createPublicHoliday()
    {
        try {
            //validator
            $validator = Validator::make($this->data, [
                'name' => 'required',
                'start_holiday_date' => 'required|date',
                'end_holiday_date' => 'required|date|after_or_equal:start_holiday_date',
            ], [
                'name.required' => 'Tên ngày nghỉ không được để trống',
                'start_holiday_date.required' => 'Ngày bắt đầu không được để trống',
                'start_holiday_date.date' => 'Ngày bắt đầu không đúng định dạng',
                'end_holiday_date.required' => 'Ngày kết thúc không được để trống',
                'end_holiday_date.date' => 'Ngày kết thúc không đúng định dạng',
                'end_holiday_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            } else {

                DB::beginTransaction();
                $public_holiday = PublicHoliday::create($this->data);
                DB::commit();
                return $public_holiday;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
