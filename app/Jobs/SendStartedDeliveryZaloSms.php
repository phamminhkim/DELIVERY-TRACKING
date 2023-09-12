<?php

namespace App\Jobs;

use App\Models\Business\Order;
use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Repositories\System\ZaloRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendStartedDeliveryZaloSms extends SendZaloSms
{
    public function __construct($customer_id, $order_id, $estimate_transport_date)
    {
        $this->customer_id = $customer_id;
        $this->template_id = config('services.zalo.template.started_delivery');
        $this->template_data = [
            'id' => $order_id,
            'order_code' => Order::find($order_id)->sap_so_number,
            'customer_name' => Customer::find($customer_id)->name,
            'transport_date' => $estimate_transport_date
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
    }
}
