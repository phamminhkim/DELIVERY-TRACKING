<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Business\Delivery;
use App\Models\Master\ExternalDeliveryCode;
use App\Utilities\ApiHandler;
use App\Utilities\ApiRequest;
use App\Utilities\ApiUtility;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExternalDeiveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:update'; // php artisan delivery:update

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update delivery status from external API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $query = Delivery::query();
            $query->where('complete_delivery_date', null)
                ->whereHas('external_code')
                ->with('external_code', 'partner',  'partner.mapping', 'delivery_info', 'orders');

            $deliveries = $query->get();
            $api_requests = $deliveries->map(function ($delivery) {
                $delivery_partner = $delivery->partner;
                $external_code = $delivery->external_code->external_delivery_code;

                $body_datas = json_decode($delivery_partner->api_body_datas, true);
                $body_datas = array_merge($body_datas, array($delivery_partner->api_delivery_code_field => $external_code));
                return new ApiRequest($delivery_partner->api_url, [], $body_datas, $delivery_partner->api_method);
            })->toArray();

            $api_customer_responses = ApiHandler::handleMultipleRequests($api_requests);

            DB::beginTransaction();
            foreach ($api_customer_responses as $index => $api_customer_response) {
                $delivery = (clone $deliveries)->slice($index, 1)->first();
                $api_mapping = $delivery->partner->mapping;
                $api_customer_response = ApiUtility::getPropertyByPath($api_customer_response, $api_mapping->root_data_field, '.');
                if ($api_mapping->is_root_string) {
                    $api_customer_response = json_decode($api_customer_response, true);
                }
                $api_customer_response = $api_customer_response[0];

                $complete_delivery_date = isset($api_customer_response[$api_mapping->complete_delivery_date]) ? Carbon::create($api_customer_response[$api_mapping->complete_delivery_date]) : null;
                $delivery->complete_delivery_date = $complete_delivery_date;
                $delivery->save();


                $delivery_infos = $delivery->delivery_info;
                $delivery_infos->each(function ($delivery_info) use ($complete_delivery_date) {
                    $delivery_info->complete_delivery_date = $complete_delivery_date;
                    $delivery_info->confirm_delivery_date = $complete_delivery_date;
                    $delivery_info->save();
                });

                $orders = $delivery->orders;
                $orders->each(function ($order) {
                    $order->status_id = OrderStatus::Delivered;
                    $order->save();
                });



                $delivery->timelines()->create([
                    'event' => 'complete_delivery',
                    'description' => 'Vận đơn đã được nhà vận chuyển ngoài hoàn thành.',
                    'timestamp' => $complete_delivery_date,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error($e->getMessage());
        }
    }
}
