<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuRoutersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_routers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon');
            $table->string('link');
            $table->integer('parent_id');
            $table->integer('order');
            $table->integer('left');
            $table->integer('right');
            $table->boolean('is_active');

            $table->index('parent_id');
            $table->index('order');
            $table->index('left');
            $table->index('right');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_routers');
    }
}
