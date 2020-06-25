<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', );
            $table->string('client_key', );
            $table->string('oauth_client_id', );
            $table->string('public_key', );
            $table->string('shared_secret', );
            $table->string('server_version', );
            $table->string('plugins_version', );
            $table->string('base_url', );
            $table->string('product_type', );
            $table->string('description', );
            $table->string('event_type', );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
