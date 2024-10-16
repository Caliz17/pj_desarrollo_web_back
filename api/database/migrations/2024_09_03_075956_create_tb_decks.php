<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_decks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('id_card_1')->nullable();
            $table->integer('id_card_2')->nullable();
            $table->integer('id_card_3')->nullable();
            $table->integer('id_card_4')->nullable();
            $table->integer('id_card_5')->nullable();
            $table->integer('id_card_6')->nullable();
            $table->integer('id_card_7')->nullable();
            $table->integer('id_card_8')->nullable();
            $table->integer('id_deck_player')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_decks');
    }
};
