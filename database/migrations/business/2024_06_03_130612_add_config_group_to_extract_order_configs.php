<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigGroupToExtractOrderConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            $table->boolean('is_config_group')->default(false)->after('convert_file_type');
            $table->boolean('is_master_config_group')->default(false)->after('is_config_group');
            $table->boolean('is_slave_config_group')->default(false)->after('is_master_config_group');
            $table->bigInteger('master_config_group_id')->nullable()->after('is_slave_config_group');
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
            $table->dropColumn('is_config_group');
            $table->dropColumn('is_master_config_group');
            $table->dropColumn('is_slave_config_group');
            $table->dropColumn('master_config_group_id');
        });
    }
}
