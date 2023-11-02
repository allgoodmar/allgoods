<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoodpayRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoodpay_refunds', function (Blueprint $table) {
            $table->id();
            $table->string('zoodpay_transaction_id');
            $table->decimal('zoodpay_refund_amount', 15, 2)->default(0);
            $table->text('zoodpay_reason')->nullable();
            $table->text('zoodpay_request_id')->nullable();
            $table->string('zoodpay_refund_id')->nullable();
            $table->text('zoodpay_status')->nullable();
            $table->text('zoodpay_declined_reason')->nullable();
            $table->text('zoodpay_created_at')->nullable();
            $table->text('zoodpay_refunded_at')->nullable();
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
        Schema::dropIfExists('zoodpay_refunds');
    }
}
