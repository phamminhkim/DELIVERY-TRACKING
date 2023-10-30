<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCompanyColumnInSapMeterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_materials', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropUnique('sap_materials_company_id_sap_code_unit_id_unique');
            $table->dropColumn('company_id');
            $table->unique(['sap_code', 'unit_id'], 'sap_materials_sap_code_unit_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_materials', function (Blueprint $table) {
            $table->foreign('company_id')->references('code')->on('companies');
        });
    }
}
