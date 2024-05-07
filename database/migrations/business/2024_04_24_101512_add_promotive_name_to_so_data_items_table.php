<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotiveNameToSoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_data_items', function (Blueprint $table) {
            $table->string('promotive_name', 50)->nullable()->after('is_promotive');
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
            $table->dropColumn('promotive_name');
        });
    }
}
