<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoodpayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoodpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('zoodpay_transaction_id');
            $table->string('zoodpay_session_token');
            $table->timestamp('zoodpay_expiry_time');
            $table->text('zoodpay_payment_url');
            $table->text('zoodpay_signature');
            $table->timestamp('zoodpay_delivered_at')->nullable();
            $table->string('zoodpay_status')->nullable();
            $table->text('zoodpay_error_message')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('status')->nullable();
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
        Schema::dropIfExists('zoodpay_transactions');
    }
}
