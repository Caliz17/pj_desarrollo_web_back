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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('level')->nullable();
            $table->integer('trophies')->nullable();
            $table->unsignedBigInteger('clan_id')->nullable();

            $table->foreign('clan_id')->references('id')->on('tb_clans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clan_id']);
            $table->dropColumn(['level', 'trophies', 'clan_id']);
        });
    }
};
