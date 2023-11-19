<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); //sender
            $table->uuid('user_bank_id'); //receiver
            $table->integer('amount');
            $table->string('transaction_code'); //random code
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('user_bank_id')->references('id')->on('user_banks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
