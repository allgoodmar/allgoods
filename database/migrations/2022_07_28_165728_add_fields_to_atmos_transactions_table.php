<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAtmosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('atmos_transactions', function (Blueprint $table) {
            $table->text('success_trans_id')->nullable();
            $table->text('terminal_id')->nullable();
            $table->text('prepay_time')->nullable();
            $table->text('confirm_time')->nullable();
            $table->text('card_id')->nullable();
            $table->text('status_code')->nullable();
            $table->text('status_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('atmos_transactions', function (Blueprint $table) {
            //
        });
    }
}
