<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToExtractOrderConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->integer('order')->nullable()->after('customer_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
