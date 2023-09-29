4<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateApiDeliveryPartnerMappings extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('api_delivery_partner_mappings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_partner_id')->constrained('delivery_partners');
                $table->string('root_data_field')->nullable();
                $table->string('start_delivery_date');
                $table->string('complete_delivery_date');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('api_delivery_partner_mappings');
        }
    }
