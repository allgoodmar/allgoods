<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportPartnerMarginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_partner_margins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_partner_id');
            $table->integer('percent')->default(0);
            $table->decimal('from', 15, 2)->default(0);
            $table->decimal('to', 15, 2)->default(0);
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
        Schema::dropIfExists('import_partner_margins');
    }
}
