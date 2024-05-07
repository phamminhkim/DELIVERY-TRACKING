<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuyCachToSapCompliances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_compliances', function (Blueprint $table) {
            $table->string('quy_cach')->nullable()->change();
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
            $table->string('quy_cach')->nullable(false)->change();
        });
    }
}
