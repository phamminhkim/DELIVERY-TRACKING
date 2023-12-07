<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToRawSoItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_so_items', function (Blueprint $table) {
            $table->float('price',18,2)->default(0.00)->after('quantity');
            $table->float('amount',18,2)->default(0.00)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_so_items', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('amount');
        });
    }
}
