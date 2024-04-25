<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class AddFulltextsearchIndexToMaterialDonatedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_donateds', function (Blueprint $table) {
            DB::statement('ALTER TABLE material_donateds ADD FULLTEXT `search_name_sapcode` (`name`, `sap_code`)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_donateds', function (Blueprint $table) {
            DB::statement('ALTER TABLE material_donateds DROP INDEX search_name_sapcode');

        });
    }
}
