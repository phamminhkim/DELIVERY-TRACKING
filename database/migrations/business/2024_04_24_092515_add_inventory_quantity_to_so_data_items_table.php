<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryQuantityToSoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_data_items', function (Blueprint $table) {
            $table->integer('inventory_quantity')->nullable()->after('is_inventory');
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
            $table->dropColumn('inventory_quantity');
        });
    }
}
