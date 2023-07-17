<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDeliveryCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate code for all deliveries that do not have code yet';

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
        DB::transaction(function () {
            $deliveries = \App\Models\Business\Delivery::all();
            $count = 0;
            foreach ($deliveries as $delivery) {
                if (!$delivery->delivery_code) {
                    $delivery_code = \App\Utilities\UniqueIdUtility::generateDeliveryUniqueCode($delivery->partner);
                    $delivery->delivery_code = $delivery_code;
                    $delivery->save();

                    $this->info('Delivery code generated for delivery ' . $delivery->id . ': ' . $delivery_code);
                    $count++;
                }
            }

            $this->info('Total delivery code generated: ' . $count);
        });


        return 0;
    }
}
