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
        Schema::create('tb_battles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_1_id')->nullable();
            $table->unsignedBigInteger('player_2_id')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->timestamp('battle_time')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('player_1_id')->references('id')->on('users');
            $table->foreign('player_2_id')->references('id')->on('users');
            $table->foreign('winner_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_battles');
    }
};
