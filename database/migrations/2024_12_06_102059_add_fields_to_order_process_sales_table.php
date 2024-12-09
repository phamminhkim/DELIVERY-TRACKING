<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOrderProcessSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('order_process_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('central_branch')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'sending', 'processing', 'completed', 'canceled'])->default('pending');
            // adđ foreign key
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        // Schema::table('order_process_sales', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('central_branch')->nullable();
        //     $table->string('description')->nullable();
        //     $table->enum('status', ['pending', 'sending', 'processing', 'completed', 'canceled'])->default('pending');
        //     // adđ foreign key
        //     $table->unsignedBigInteger('created_by');
        //     $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_process_sales');
        // Schema::table('order_process_sales', function (Blueprint $table) {
        //     $table->dropColumn(['title', 'central_branch', 'description', 'status']);
        //     $table->dropForeign(['created_by']);
        // });
    }
}
