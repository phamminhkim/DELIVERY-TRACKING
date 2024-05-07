<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class AddFulltextsearchIndexToSapCompliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_compliances', function (Blueprint $table) {
            DB::statement('ALTER TABLE sap_compliances ADD FULLTEXT `search` (`name`, `sap_code`,`bar_code`)');

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
            DB::statement('ALTER TABLE sap_compliances DROP INDEX search');

        });
    }
}
