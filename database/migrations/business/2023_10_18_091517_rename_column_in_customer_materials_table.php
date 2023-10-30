<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnInCustomerMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_materials', function (Blueprint $table) {
            $table->renameColumn('po_code', 'customer_sku_code');
            $table->renameColumn('name', 'customer_sku_name');
            $table->renameColumn('unit', 'customer_sku_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_materials', function (Blueprint $table) {
            $table->renameColumn('customer_sku_code', 'po_code');
            $table->renameColumn('customer_sku_name', 'name');
            $table->renameColumn('customer_sku_unit', 'unit');
        });
    }
}
