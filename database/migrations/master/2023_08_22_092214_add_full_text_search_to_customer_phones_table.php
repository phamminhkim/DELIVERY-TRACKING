<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class AddFullTextSearchToCustomerPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_phones', function (Blueprint $table) {
            DB::statement('ALTER TABLE customer_phones ADD FULLTEXT `search` (`phone_number`, `name`, `description`)'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_phones', function (Blueprint $table) {
            DB::statement('ALTER TABLE customer_phones DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
            
        });
    }
}
