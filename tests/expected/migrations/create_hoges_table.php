<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHogesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hoges', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('email', 200);
            $table->char('address', 100)->nullable();
            $table->boolean('is_active')->nullable()->default(0);
            $table->binary('token')->nullable();
            $table->dateTime('create_at')->nullable()->comment('The created date');
            $table->primary(['user_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hoges');
    }
}
