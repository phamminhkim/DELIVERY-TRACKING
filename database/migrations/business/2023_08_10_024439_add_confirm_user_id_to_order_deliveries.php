<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmUserIdToOrderDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger("confirm_user_id")->nullable();
            $table->foreign("confirm_user_id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_deliveries', function (Blueprint $table) {
            $table->dropForeign(['confirm_user_id']);
            $table->dropColumn('confirm_user_id');
        });
    }
}
