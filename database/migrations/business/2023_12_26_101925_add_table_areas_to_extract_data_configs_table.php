<?php

use App\Enums\Ai\Extract\CamelotFlavor;
use App\Enums\Ai\Extract\ExtractMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableAreasToExtractDataConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_data_configs', function (Blueprint $table) {
            $table->boolean('is_specify_table_area')->default(false);
            $table->json('table_area_info')->nullable();
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
            $table->dropColumn('is_specify_table_area');
            $table->dropColumn('table_area_info');
        });
    }
}
