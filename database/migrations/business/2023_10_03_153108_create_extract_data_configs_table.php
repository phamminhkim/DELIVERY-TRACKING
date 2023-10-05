<?php

use App\Enums\Ai\Extract\CamelotFlavor;
use App\Enums\Ai\Extract\ExtractMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtractDataConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extract_data_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('method', ExtractMethod::getAllConstants())->default(ExtractMethod::CAMELOT);
            $table->enum('camelot_flavor', CamelotFlavor::getAllConstants())->nullable();
            $table->boolean('is_merge_pages')->default(false);
            $table->integer('exclude_head_tables_count')->default(0);
            $table->integer('exclude_tail_tables_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extract_data_configs');
    }
}
