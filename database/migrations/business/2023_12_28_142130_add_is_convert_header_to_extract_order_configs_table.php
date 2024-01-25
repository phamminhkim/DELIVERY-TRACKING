<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsConvertHeaderToExtractOrderConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->boolean('is_convert_header')->after('reference_id')->default(false);
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
            $table->dropColumn('is_convert_header');
        });
    }
}
