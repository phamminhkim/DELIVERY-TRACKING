<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyFullTextSearchInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            DB::statement('ALTER TABLE customers DROP INDEX search');
            DB::statement('ALTER TABLE customers ADD FULLTEXT `search` (`code`, `name`, `email`, `phone_number`)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            DB::statement('ALTER TABLE customers DROP INDEX search');

            DB::statement('ALTER TABLE customers ADD FULLTEXT `search` (`code`, `name`, `email`, `phone_number`, `address`)');
        });
    }
}
