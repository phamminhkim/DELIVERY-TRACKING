<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_phones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("customer_id");
            $table->foreign("customer_id")->references("id")->on("customers");
            $table->string("phone_number", 20);
            $table->string("name", 50);
            $table->string("description", 200);
            $table->boolean("is_active");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_phones');
    }
}
