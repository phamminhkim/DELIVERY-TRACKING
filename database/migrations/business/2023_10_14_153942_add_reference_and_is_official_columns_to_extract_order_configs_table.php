<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceAndIsOfficialColumnsToExtractOrderConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->foreignId('reference_id')->nullable()->references('id')->on('extract_order_configs');
            $table->boolean('is_official')->default(false)->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->dropColumn('reference_id');
            $table->dropColumn('is_official');
        });
    }
}
