<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawPoHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_po_headers', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50);
            $table->string('convert_po_uid', 50);
            $table->string('po_delivery_date', 10)->nullable();
            $table->string('sap_so_number', 50)->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_code', 50)->nullable();
            $table->string('note', 250)->nullable();
            $table->string('level2', 50)->nullable();
            $table->string('level3', 50)->nullable();
            $table->string('level4', 50)->nullable();
            $table->unsignedBigInteger("created_by");
            $table->foreign("created_by")->references("id")->on("users");
            $table->boolean('is_deleted')->default(false);
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
        Schema::dropIfExists('raw_po_headers');
    }
}
