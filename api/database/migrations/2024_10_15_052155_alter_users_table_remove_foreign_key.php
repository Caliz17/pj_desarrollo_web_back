<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableRemoveForeignKey extends Migration
{
    public function up()
    {
        // Eliminar la clave foránea
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clan_id']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('clan_id')->nullable()->after('trophies');
            $table->foreign('clan_id')->references('id')->on('tb_clans')->onDelete('cascade');
        });
    }
}
