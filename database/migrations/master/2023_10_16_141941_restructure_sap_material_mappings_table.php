<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestructureSapMaterialMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_material_mappings', function (Blueprint $table) {
            // $table->dropColumn('created_at');
            // $table->dropColumn('updated_at');
            // $table->dropForeign('customer_id');
            // $table->dropColumn('customer_id');
            // $table->dropColumn('sap_code');
            // $table->dropColumn('customer_code');
            // $table->foreignId('customer_material_id')->constrained('customer_materials');
            // $table->foreignId('sap_material_id')->constrained('sap_materials');
            // $table->integer('percentage');
            $table->dropIfExists();
        });

        Schema::create('sap_material_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_material_id')->constrained('customer_materials');
            $table->foreignId('sap_material_id')->constrained('sap_materials');
            $table->integer('percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_material_mappings', function (Blueprint $table) {

            $table->dropIfExists();
        });
    }
}
