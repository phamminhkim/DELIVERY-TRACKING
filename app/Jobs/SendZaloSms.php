<?php

namespace App\Jobs;

use App\Models\Business\ZaloSmsLog;
use App\Models\Master\CustomerPhone;
use App\Repositories\System\ZaloRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendZaloSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer_id;
    protected $template_id;
    protected $template_data;

    public function __construct($customer_id, $template_id, $template_data)
    {
        $this->customer_id = $customer_id;
        $this->template_id = $template_id;
        $this->template_data = $template_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer_phones = CustomerPhone::where('customer_id', $this->customer_id)->where('is_active', true)->where('is_receive_sms', true)->get();
        foreach ($customer_phones as $customer_phone) {
            $phone = $customer_phone->phone;
            $request = [
                'customer_id' => $this->customer_id,
                'phone' => $phone,
                'template_id' => $this->template_id,
                'template_data' => json_encode($this->template_data),
            ];
            if (config('services.zalo.sms_mode') == 'development') {
                $request['mode'] = 'development';
            }
            $log = ZaloSmsLog::create($request);
            $response = ZaloRepository::sendZaloSmsWithTemplate($phone, $this->template_id, $this->template_data);
            if ($response['message'] == 'success') {
                $log->is_success = true;
                $log->save();
            }
        }
    }
}
