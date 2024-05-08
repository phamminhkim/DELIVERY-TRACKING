<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class AddFulltextsearchIndexToCustomerMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_materials', function (Blueprint $table) {
            DB::statement('ALTER TABLE customer_materials ADD FULLTEXT `search_customer` (`customer_sku_code`, `customer_sku_name`,`customer_sku_unit`)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_materials', function (Blueprint $table) {
            DB::statement('ALTER TABLE customer_materials DROP INDEX search_customer');

        });
    }
}
