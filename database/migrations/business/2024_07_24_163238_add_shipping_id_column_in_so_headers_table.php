<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingIdColumnInSoHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_headers', function (Blueprint $table) {
            $table->string('shipping_id')->after('warehouse_id')->nullable();
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
            $table->dropColumn('shipping_id');
        });
    }
}
