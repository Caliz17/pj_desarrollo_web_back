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
        Schema::create('tb_card_decks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deck_id')->nullable();
            $table->unsignedBigInteger('card_id')->nullable();
            $table->integer('position')->nullable();
            $table->foreign('deck_id')->references('id')->on('tb_decks');
            $table->foreign('card_id')->references('id')->on('tb_cards');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_card_decks');
    }
};
