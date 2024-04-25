<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFulltextsearchIndexToMaterialCombosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_combos', function (Blueprint $table) {
            DB::statement('ALTER TABLE material_combos ADD FULLTEXT `search_name_code_barcode` (`name`, `sap_code`,`bar_code`)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_combos', function (Blueprint $table) {
            DB::statement('ALTER TABLE material_combos DROP INDEX search_name_code_barcode');
        });
    }
}
