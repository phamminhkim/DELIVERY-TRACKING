<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSapCodeNotNullToSapCompliances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_compliances', function (Blueprint $table) {
            $table->string('sap_code')->nullable(false)->change(); // Thay đổi cột sap_code để không chấp nhận giá trị null
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
            $table->string('sap_code')->nullable(true)->change(); // Thay đổi cột sap_code để chấp nhận giá trị null
        });
    }
}
