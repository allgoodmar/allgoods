<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYoutubelinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtubelinks', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable()->default(null);
            $table->string('name');
            $table->string('link');
            $table->string('thumbnail');
            $table->integer('order')->default(900);
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
        Schema::dropIfExists('youtubelinks');
    }
}
