<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerNumberAndConversionRateSapToSapMaterialMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_material_mappings', function (Blueprint $table) {
            $table->integer('customer_number')->after('customer_material_id')->nullable();
            $table->integer('conversion_rate_sap')->after('sap_material_id')->nullable();
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
            $table->dropColumn('customer_number');
            $table->dropColumn('conversion_rate_sap');
        });
    }
}
