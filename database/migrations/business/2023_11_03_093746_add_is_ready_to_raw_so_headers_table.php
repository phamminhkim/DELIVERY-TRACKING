<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReadyToRawSoHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_so_headers', function (Blueprint $table) {
            $table->boolean('is_wating_sync')->default(false)->after('is_promotive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_so_headers', function (Blueprint $table) {
            $table->dropColumn('is_wating_sync');
        });
    }
}
