<?php

use App\Models\Business\Delivery;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdToDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger("customer_id")->after('delivery_partner_id');
        });

        DB::transaction(function () {
            Delivery::all()->each(function ($delivery) {
                $customer_id = $delivery->orders->first()->customer->id;
                $delivery->update(['customer_id' => $customer_id]);
            });
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreign("customer_id")->references("id")->on("customers");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
}
