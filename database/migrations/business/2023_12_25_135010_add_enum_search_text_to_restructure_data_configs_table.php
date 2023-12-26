<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEnumSearchTextToRestructureDataConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `restructure_data_configs` CHANGE `method` `method` ENUM('arraymappingbyindex','arraymappingbykey','arraymappingbymergeindex','arraymappingbysearchtext') NOT NULL DEFAULT 'arraymappingbyindex'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
