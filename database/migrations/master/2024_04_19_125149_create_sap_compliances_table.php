<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapCompliancesTable extends Migration
{
    public function up()
    {
        Schema::create('sap_compliances', function (Blueprint $table) {
            $table->id();
            $table->string("bar_code", 50)->nullable()->unique();
            $table->string('sap_code', 40)->unique()->nullable();
            $table->string('name', 255);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('sap_units')->onDelete('cascade');
            $table->string('quy_cach', 255);
            $table->boolean('check_pc')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sap_compliances');
    }
}
