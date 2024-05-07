<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCodeToMaterialCategoryTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_category_types', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_category_types', function (Blueprint $table) {
            $table->string('code')->nullable(); // Thêm lại cột code với kiểu dữ liệu và thuộc tính tùy chọn tương ứng
        });
    }
}
