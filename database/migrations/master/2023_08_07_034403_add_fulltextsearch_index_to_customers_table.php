<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFulltextsearchIndexToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            DB::statement('ALTER TABLE customers ADD FULLTEXT `search` (`code`, `name`, `email`, `phone_number`, `address`)'); //đánh index cho cột name

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
            DB::statement('ALTER TABLE customers DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
            
        });
    }
}
