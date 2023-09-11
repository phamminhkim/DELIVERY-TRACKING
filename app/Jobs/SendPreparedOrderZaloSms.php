<?php

namespace App\Jobs;

use App\Models\Business\Order;
use App\Models\Master\CustomerPhone;
use App\Repositories\System\ZaloRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPreparedOrderZaloSms extends SendZaloSms
{
    public function __construct($customer_id, $order_id)
    {
        $this->customer_id = $customer_id;
        $this->template_id = config('services.zalo.template.prepared_order');
        $this->template_data = [
            'id' => $order_id,
            'order_code' => Order::first($order_id)->sap_so_number,
            'customer_name' => CustomerPhone::first($customer_id)->name,
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
