<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReprogressColumnToFileExtractErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_extract_errors', function (Blueprint $table) {
            $table->boolean('is_reprogress')->default(false)->after('extract_error_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_extract_errors', function (Blueprint $table) {
            $table->dropColumn('is_reprogress');
        });
    }
}
