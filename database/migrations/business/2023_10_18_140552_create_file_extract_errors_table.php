<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileExtractErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_extract_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_file_id')->constrained('uploaded_files');
            $table->foreignId('extract_error_id')->constrained('extract_errors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_extract_errors');
    }
}
