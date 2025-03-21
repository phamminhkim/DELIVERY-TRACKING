<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReceivedAtNullableInOrderProcessSaleBiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_process_sale_receives', function (Blueprint $table) {
            $table->dateTime('received_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_process_sale_receives', function (Blueprint $table) {
            $table->dateTime('received_at')->change();
        });
    }
}
