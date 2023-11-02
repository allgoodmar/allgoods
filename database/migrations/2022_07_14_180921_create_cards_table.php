<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('atmos_card_id')->nullable();
            $table->string('atmos_card_token')->nullable();
            $table->text('atmos_transaction_id')->nullable();
            $table->string('pan')->nullable();
            $table->string('expiry')->nullable();
            $table->text('card_holder')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->text('phone')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('is_default')->default(0);
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('cards');
    }
}
