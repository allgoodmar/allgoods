<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlifshopApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alifshop_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('application_status_id')->nullable();
            $table->string('application_status_key')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('down_payment_amount')->nullable();
            $table->text('prepayment')->nullable();
            $table->decimal('discount', 15, 2)->default(0);
            $table->integer('duration')->default(1);
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
        Schema::dropIfExists('alifshop_applications');
    }
}
