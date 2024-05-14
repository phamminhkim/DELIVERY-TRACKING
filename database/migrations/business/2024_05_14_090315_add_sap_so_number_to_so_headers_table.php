<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapSoNumberToSoHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_headers', function (Blueprint $table) {
            $table->string('sap_so_number', 50)->after('order_process_id');
            $table->string('po_number', 50)->after('sap_so_number');
            $table->string('po_delivery_date', 10)->after('po_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('so_headers', function (Blueprint $table) {
            $table->dropColumn('sap_so_number');
            $table->dropColumn('po_number');
            $table->dropColumn('po_delivery_date');
        });
    }
}
