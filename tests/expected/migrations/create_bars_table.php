<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->text('content')->nullable();
            $table->date('publish_date')->nullable();
            $table->unsignedBigInteger('author_id')->nullable()->default(1);
            $table->float('rate', 10, 0)->nullable();
            $table->unsignedDecimal('score', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['name'], 'unique_name');
            $table->index(['name', 'publish_date'], 'index_name_publish_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bars');
    }
}
