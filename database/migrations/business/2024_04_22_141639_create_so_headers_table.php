<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('so_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_process_id')->constrained('order_processes');
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_code', 50)->nullable();
            $table->float('company_price')->nullable();
            $table->string('note', 250)->nullable();
            $table->string('level2', 50)->nullable();
            $table->string('level3', 50)->nullable();
            $table->string('level4', 50)->nullable();
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
        Schema::dropIfExists('so_headers');
    }
}
