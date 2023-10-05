<?php

use App\Enums\AI\Restructure\RestructureMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestructureDataConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restructure_data_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('method', RestructureMethod::getAllConstants())->default(RestructureMethod::INDEXARRAYMAPPING);
            $table->json('structure');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restructure_data_configs');
    }
}
