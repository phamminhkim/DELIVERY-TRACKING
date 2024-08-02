<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityLevelColumnToSapMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_materials', function (Blueprint $table) {
            $table->string('priority')->after('name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sap_materials', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
