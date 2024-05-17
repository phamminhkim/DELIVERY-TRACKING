<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSapCodeToSapCompliances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_compliances', function (Blueprint $table) {
            $table->dropUnique('sap_compliances_sap_code_unique'); // Xóa ràng buộc unique
            $table->string('sap_code')->nullable()->change(); // Sửa đổi cột sap_code để cho phép giá trị null
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
            $table->string('sap_code')->unique()->change(); // Thêm lại ràng buộc unique
        });
    }
}
