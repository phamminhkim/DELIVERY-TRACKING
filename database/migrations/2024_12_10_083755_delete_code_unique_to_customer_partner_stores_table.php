<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteCodeUniqueToCustomerPartnerStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_partner_stores', function (Blueprint $table) {
            $table->dropUnique('customer_partner_stores_code_unique');
            // add unique name
            $table->unique('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_partner_stores', function (Blueprint $table) {
            $table->unique('code');
            $table->dropUnique('customer_partner_stores_name_unique');
        });
    }
}
