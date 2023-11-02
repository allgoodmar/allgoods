<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentPlanProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installment_plan_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('installment_plan_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->foreign('installment_plan_id')->references('id')->on('installment_plans')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installment_plan_product');
    }
}
