<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCheckPcToSapCompliances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_compliances', function (Blueprint $table) {
            $table->renameColumn('check_pc', 'check_qc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_compliances', function (Blueprint $table) {
            $table->renameColumn('check_qc', 'check_pc');
        });
    }
}
