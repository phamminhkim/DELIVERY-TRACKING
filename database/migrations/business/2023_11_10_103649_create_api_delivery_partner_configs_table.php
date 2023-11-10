<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiDeliveryPartnerConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_delivery_partner_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_partner_id')->constrained('delivery_partners');
            $table->foreignId('api_request_id')->constrained('api_delivery_partner_requests');
            $table->foreignId('api_mapping_id')->constrained('api_delivery_partner_mappings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_delivery_partner_configs');
    }
}
