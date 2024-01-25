<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderConfigsToExtractOrderConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->foreignId('extract_header_config_id')->after('restructure_data_config_id')->nullable()->constrained('extract_data_configs');
            $table->foreignId('convert_table_header_config_id')->after('extract_header_config_id')->nullable()->constrained('convert_table_configs');
            $table->foreignId('restructure_header_config_id')->after('convert_table_header_config_id')->nullable()->constrained('restructure_data_configs');
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
            $table->dropForeign(['extract_header_config_id', 'convert_table_header_config_id', 'restructure_header_config_id']);
            $table->dropColumn('extract_header_config_id');
            $table->dropColumn('convert_table_header_config_id');
            $table->dropColumn('restructure_header_config_id');
        });
    }
}
