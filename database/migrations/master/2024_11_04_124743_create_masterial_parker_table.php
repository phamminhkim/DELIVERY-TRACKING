<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterialParkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_parkers', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('customer_group_id')->nullable()->constrained('customer_groups');            
            $table->string('bar_code', 50)->nullable();
            $table->string('sap_code', 40)->nullable();
            $table->string('name', 255);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masterial_parkers');
    }
}
