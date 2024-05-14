<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToSoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_data_items', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('so_header_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('so_data_items', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
