<?php

use App\Enums\Ai\Extract\CamelotFlavor;
use App\Enums\Ai\Extract\ExtractMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecifyTableNumberToExtractDataConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_data_configs', function (Blueprint $table) {
            $table->integer('specify_table_number')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extract_data_configs', function (Blueprint $table) {
            $table->dropColumn('specify_table_number');
        });
    }
}
