<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIsDeletedToIsActiveInMaterialDonateds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_donateds', function (Blueprint $table) {
            // Rename the column
            $table->renameColumn('is_deleted', 'is_active');
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
            // Rename the column back to its original name
            $table->renameColumn('is_active', 'is_deleted');
        });
    }
}
