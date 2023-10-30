<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUploadedFileIdToRawExractHeaderIdInRawExtractItemmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_extract_items', function (Blueprint $table) {
            $table->dropForeign(['uploaded_file_id']);
            $table->dropColumn('uploaded_file_id');
            $table->foreignId('raw_extract_header_id')->constrained('raw_extract_headers')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_extract_items', function (Blueprint $table) {
            $table->dropForeign(['raw_extract_header_id']);
            $table->dropColumn('raw_extract_header_id');
            $table->foreignId('uploaded_file_id')->constrained('uploaded_files')->after('id');
        });
    }
}
