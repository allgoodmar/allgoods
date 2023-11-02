<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('order_min_price', 15, 2)->default(0);
            $table->decimal('order_max_price', 15, 2)->default(0);
            $table->integer('delivery_days')->default(0);
            $table->integer('delivery_hours')->default(0);
            $table->integer('delivery_minutes')->default(0);
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('order')->default(0);
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
        Schema::dropIfExists('shipping_methods');
    }
}
