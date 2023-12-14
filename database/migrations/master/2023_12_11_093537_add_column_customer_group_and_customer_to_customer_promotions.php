<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCustomerGroupAndCustomerToCustomerPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_promotions', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('customer_group_id')->nullable()->after('id')->constrained('customer_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_promotions', function (Blueprint $table) {

            $table->dropForeign(['customer_group_id']);
            $table->dropColumn('customer_group_id');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
}
