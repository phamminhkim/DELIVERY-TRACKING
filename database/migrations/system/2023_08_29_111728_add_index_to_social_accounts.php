<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToSocialAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->index(['provider', 'provider_user_id'], 'social_accounts_provider_provider_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropIndex('social_accounts_provider_provider_user_id_index');
        });
    }
}
