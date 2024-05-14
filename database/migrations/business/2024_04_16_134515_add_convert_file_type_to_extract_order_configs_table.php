<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConvertFileTypeToExtractOrderConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_order_configs', function (Blueprint $table) {
            // $table->string('convert_file_type',10)->nullable()->default('pdf')->after('is_convert_header');
            if (!Schema::hasColumn('extract_order_configs', 'convert_file_type')) {
                $table->string('convert_file_type',10)->nullable()->default('pdf')->after('is_convert_header');
            }
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
            if (Schema::hasColumn('extract_order_configs', 'convert_file_type')) {
                $table->dropColumn('convert_file_type');
            }
        });
    }
}
