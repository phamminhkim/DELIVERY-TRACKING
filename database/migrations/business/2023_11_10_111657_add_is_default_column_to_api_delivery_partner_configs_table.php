<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDefaultColumnToApiDeliveryPartnerConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_delivery_partner_configs', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_delivery_partner_configs', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
}
