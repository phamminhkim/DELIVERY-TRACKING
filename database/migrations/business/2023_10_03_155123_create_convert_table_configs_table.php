<?php

use  App\Enums\AI\Convert\ConvertMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConvertTableConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convert_table_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('method', ConvertMethod::getAllConstants())->default(ConvertMethod::MANUAL);
            $table->json('manual_patterns')->nullable();
            $table->string('regex_pattern')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convert_table_configs');
    }
}
