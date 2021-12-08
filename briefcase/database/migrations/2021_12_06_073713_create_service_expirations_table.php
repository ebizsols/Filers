<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceExpirationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_expirations', function (Blueprint $table) {
            $table->id();
            $table->timestamp('expiry_date')->format('Y-m-d');
            $table->text('message')->nullable();
            $table->enum('type', ['admin', 'employee', 'client']);
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
        Schema::dropIfExists('service_expirations');
    }
}
