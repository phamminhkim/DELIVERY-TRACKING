<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRootStringColumnToApiDeliveryPartnerMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_delivery_partner_mappings', function (Blueprint $table) {
            $table->boolean('is_root_string')->default(false)->after('root_data_field');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_delivery_partner_mappings', function (Blueprint $table) {
            $table->dropColumn('is_root_string');
        });
    }
}
