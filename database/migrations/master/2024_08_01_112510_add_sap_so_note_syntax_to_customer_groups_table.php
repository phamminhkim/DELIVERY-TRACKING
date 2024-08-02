<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapSoNoteSyntaxToCustomerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->json('sap_so_note_syntax')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->dropColumn('sap_so_note_syntax');
            $table->dropColumn('note');
        });
    }
}
