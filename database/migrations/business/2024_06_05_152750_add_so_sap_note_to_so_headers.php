<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoSapNoteToSoHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_headers', function (Blueprint $table) {
            $table->string('so_sap_note')->nullable()->after('so_uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('so_headers', function (Blueprint $table) {
            $table->dropColumn('so_sap_note');
        });
    }
}
