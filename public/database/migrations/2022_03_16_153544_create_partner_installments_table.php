<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            $table->integer('duration')->default(0);
            $table->integer('percent')->default(0);
            $table->decimal('min_price', 15, 2)->default(0);
            $table->decimal('max_price', 15, 2)->default(0);
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('order')->default(0);
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_installments');
    }
}
