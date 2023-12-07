<?php

use App\Enums\ApiRequestMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiDeliveryPartnerRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_delivery_partner_requests', function (Blueprint $table) {
            $table->id();
            $table->string('api_url', 200);
            $table->enum('api_method', ApiRequestMethod::methods())->default(ApiRequestMethod::GET)->comment('GET, POST, PUT, DELETE');
            $table->json('api_body_datas');
            $table->string('api_delivery_code_field')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_delivery_partner_requests');
    }
}
