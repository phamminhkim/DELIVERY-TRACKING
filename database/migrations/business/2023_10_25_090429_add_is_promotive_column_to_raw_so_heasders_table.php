<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPromotiveColumnToRawSoHeasdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_so_headers', function (Blueprint $table) {
            $table->boolean('is_promotive')->default(false)->after('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_so_headers', function (Blueprint $table) {
            $table->dropColumn('is_promotive');
        });
    }
}
