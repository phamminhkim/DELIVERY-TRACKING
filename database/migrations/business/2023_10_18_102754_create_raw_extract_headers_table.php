<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawExtractHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_extract_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('uploaded_file_id')->constrained('uploaded_files');
            $table->string('po_number')->nullable();
            $table->dateTime('po_date')->nullable();
            $table->string('po_person')->nullable();
            $table->string('po_phone')->nullable();
            $table->dateTime('po_delivery_date')->nullable();
            $table->text('po_delivery_address')->nullable();
            $table->string('po_note')->nullable();
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
        Schema::dropIfExists('raw_extract_headers');
    }
}
