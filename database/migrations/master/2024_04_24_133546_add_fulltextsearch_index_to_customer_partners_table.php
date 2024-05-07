<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFulltextsearchIndexToCustomerPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_partners', function (Blueprint $table) {
                DB::statement('ALTER TABLE customer_partners ADD FULLTEXT `search_name_code_note` (`name`, `code`,`note`)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_partners', function (Blueprint $table) {
            DB::statement('ALTER TABLE customer_partners DROP INDEX search_name_code_note');
        });
    }
}
