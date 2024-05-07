<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePricesInSoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_data_items', function (Blueprint $table) {
            $table->float('price_po',18,2)->change();
            $table->float('amount_po',18,2)->change();
            $table->float('company_price',18,2)->change();
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
            $table->float('price_po',8,2)->change();
            $table->float('amount_po',8,2)->change();
            $table->float('company_price',8,2)->change();
        });
    }
}
