<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdToOrderChildrens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });

        Schema::table('order_sales', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });

        Schema::table('order_approveds', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });

        Schema::table('order_receivers', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_receivers', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('order_approveds', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('order_sales', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
}
