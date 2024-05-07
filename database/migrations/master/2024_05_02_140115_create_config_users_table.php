<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_users', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->string('code',20);
            $table->string('value',10);
            $table->timestamps();
            $table->primary(['user_id', 'code']);
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_users');
    }
}
