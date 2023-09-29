<?php

use App\Enums\ApiRequestMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiMethodColumnToDeliveryPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_partners', function (Blueprint $table) {
            $table->enum('api_method', ApiRequestMethod::methods())->after('api_url')->default(ApiRequestMethod::GET)->comment('GET, POST, PUT, DELETE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_partners', function (Blueprint $table) {
            $table->dropColumn('api_method');
        });
    }
}
