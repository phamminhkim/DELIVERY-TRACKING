<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_materials', function (Blueprint $table) {
            $table->id();
            $table->string('company_id', 4);
            $table->unsignedBigInteger('unit_id');
            $table->string('sap_code', 40);
            $table->string('name');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('code')->on('companies');
            $table->foreign('unit_id')->references('id')->on('sap_units');
            $table->unique(['company_id', 'sap_code', 'unit_id'], 'sap_materials_company_id_sap_code_unit_id_unique');
            $table->index('sap_code', 'sap_materials_sap_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_materials');
    }
}
