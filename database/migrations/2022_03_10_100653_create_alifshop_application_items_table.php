<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlifshopApplicationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alifshop_application_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('alifshop_application_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('good')->nullable();
            $table->string('good_type')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('sku')->nullable();
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
        Schema::dropIfExists('alifshop_application_items');
    }
}
