<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTokenScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_token_scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('token_id')->nullable();
            $table->foreign('token_id')->references('id')->on('delivery_tokens');
            $table->unsignedBigInteger('scan_by');
            $table->foreign('scan_by')->references('id')->on('users');
            $table->dateTime('scan_at');
            $table->boolean('is_success');
            $table->string('result')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_token_scans');
    }
}
