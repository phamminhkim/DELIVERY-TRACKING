<?php

use  App\Enums\AI\Convert\ConvertMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWithoutHeaderToConvertTableConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('convert_table_configs', function (Blueprint $table) {
            $table->boolean('is_without_header')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('convert_table_configs', function (Blueprint $table) {
            $table->dropColumn('is_without_header');
        });
    }
}
