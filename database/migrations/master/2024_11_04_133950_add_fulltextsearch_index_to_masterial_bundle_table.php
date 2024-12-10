<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFulltextsearchIndexToMasterialBundleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_bundles', function (Blueprint $table) {
            DB::statement('ALTER TABLE material_bundles ADD FULLTEXT `search_name_code_barcode` (`name`, `sap_code`,`bar_code`)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_bundles', function (Blueprint $table) {
            DB::statement('ALTER TABLE material_bundles DROP INDEX search_name_code_barcode');
        });
    }
}
