<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalDeliveryCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_delivery_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->string('external_delivery_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_delivery_codes');
    }
}
