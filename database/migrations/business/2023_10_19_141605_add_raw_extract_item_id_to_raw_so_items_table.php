<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRawExtractItemIdToRawSoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_so_items', function (Blueprint $table) {
            $table->foreignId('raw_extract_item_id')->nullable()->after('id')->constrained('raw_extract_items');
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
            $table->dropForeign(['raw_extract_item_id']);
            $table->dropColumn('raw_extract_item_id');
        });
    }
}
