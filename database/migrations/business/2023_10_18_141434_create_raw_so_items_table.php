<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawSoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_so_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_so_header_id')->constrained('raw_so_headers');
            $table->foreignId('sap_material_id')->constrained('sap_materials');
            $table->float('quantity')->nullable();
            $table->string('note')->nullable();
            $table->integer('percentage');
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
        Schema::dropIfExists('raw_so_items');
    }
}
