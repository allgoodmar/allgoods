<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallmentFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('passport_main_image')->nullable();
            $table->unsignedBigInteger('passport_address_image')->nullable();
            $table->unsignedBigInteger('card_number')->nullable();
            $table->integer('card_expiry')->nullable();
            $table->tinyInteger('installment_data_verified')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['passport_main_image', 'passport_address_image', 'card_number', 'card_expiry', 'installment_data_verified']);
        });
    }
}
