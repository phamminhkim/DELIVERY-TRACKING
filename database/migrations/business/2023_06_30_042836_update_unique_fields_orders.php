<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUniqueFieldsOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unique(['sap_so_number']);
            $table->unique(['sap_do_number']);
            $table->index(['sap_po_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['sap_po_number']);
            $table->dropUnique(['sap_do_number']);
            $table->dropUnique(['sap_so_number']);
        });
    }
}
