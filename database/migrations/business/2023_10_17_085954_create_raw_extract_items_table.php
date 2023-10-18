<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawExtractItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_extract_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_material_id')->references('id')->on('customer_materials');
            $table->integer('quantity');
            $table->foreignId('uploaded_file_id')->references('id')->on('uploaded_files');
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
        Schema::dropIfExists('raw_extract_items');
    }
}
