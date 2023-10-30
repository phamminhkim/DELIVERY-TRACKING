<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueSerialNumberColumnToRawSoHeadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_so_headers', function (Blueprint $table) {
            $table->renameColumn('searial_number', 'serial_number');
            $table->unique(['serial_number']);
            $table->unique(['sap_so_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_so_headers', function (Blueprint $table) {
            $table->dropUnique(['serial_number']);
            $table->dropUnique(['sap_so_number']);
            $table->renameColumn('serial_number', 'searial_number');
        });
    }
}
