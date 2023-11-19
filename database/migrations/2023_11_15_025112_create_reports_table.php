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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Suspect Data
            $table->uuid('bank_id');
            $table->string('suspect_account_name');
            $table->string('suspect_account_number');
            $table->string('suspect_phone')->nullable();

            // Chronology
            $table->uuid('platform_id');
            $table->uuid('product_category_id');
            $table->text('chronology')->required();
            $table->integer('loss_amount');

            // Reporter Data
            $table->uuid('user_id');
            $table->string('reporter_name');
            $table->enum('identity', ['ktp', 'sim', 'passport']);
            $table->string('identity_number');
            $table->string('reporter_phone');

            $table->timestamps();

            $table->foreign('bank_id')->references('id')->on('banks');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('product_category_id')->references('id')->on('product_categories');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
