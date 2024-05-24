<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantitySapToSoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_data_items', function (Blueprint $table) {
            $table->float('quantity3_sap')->nullable()->after('quantity2_po');
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
            $table->dropColumn('quantity3_sap');
        });
    }
}
