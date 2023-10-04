<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtractOrderConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extract_order_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_group_id')->constrained('customer_groups');
            $table->foreignId('extract_data_config_id')->constrained('extract_data_configs');
            $table->foreignId('convert_table_config_id')->constrained('convert_table_configs');
            $table->foreignId('restructure_data_config_id')->constrained('restructure_data_configs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extract_order_configs');
    }
}
