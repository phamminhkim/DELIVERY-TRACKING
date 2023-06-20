<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid("delivery_id");
            $table->foreign("delivery_id")->references("id")->on("deliveries");
            $table->unsignedBigInteger("delivery_partner_id");
            $table->foreign("delivery_partner_id")->references("id")->on("delivery_partners");
            $table->string("token", 100);
            $table->boolean("is_primary");
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
        Schema::dropIfExists('delivery_tokens');
    }
}
