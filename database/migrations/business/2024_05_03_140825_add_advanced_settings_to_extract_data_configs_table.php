<?php

use App\Enums\Ai\Extract\CamelotFlavor;
use App\Enums\Ai\Extract\ExtractMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvancedSettingsToExtractDataConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extract_data_configs', function (Blueprint $table) {
            $table->boolean('is_specify_advanced_settings')->default(false);
            $table->json('advanced_settings_info')->nullable();
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
            $table->dropColumn('is_specify_advanced_settings');
            $table->dropColumn('advanced_settings_info');
        });
    }
}
