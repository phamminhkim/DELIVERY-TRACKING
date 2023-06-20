<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->string("company_code", 4);
            $table->foreign("company_code")->references("code")->on("companies");
            $table->dateTime("start_delivery_date")->nullable();
            $table->dateTime("complete_delivery_date")->nullable();
            $table->unsignedBigInteger("delivery_partner_id");
            $table->foreign("delivery_partner_id")->references("id")->on("delivery_partners");
            $table->unsignedBigInteger("created_by");
            $table->foreign("created_by")->references("id")->on("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
