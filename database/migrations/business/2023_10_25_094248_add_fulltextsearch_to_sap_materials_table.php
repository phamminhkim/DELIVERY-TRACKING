<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFulltextsearchToSapMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_materials', function (Blueprint $table) {
            DB::statement('ALTER TABLE sap_materials ADD FULLTEXT `search` (`sap_code`, `name`)'); //đánh index cho cột name, sap_code
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
            DB::statement('ALTER TABLE sap_materials DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
        });
    }
}
