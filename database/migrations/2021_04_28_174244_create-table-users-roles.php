<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsersRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('users_roles', function (Blueprint $table) {
            $table->string('uid');
            $table->string("rid");
            $table->boolean("active")->default(false);
            $table->foreign('uid')->references('mauser')->on('users')->onDelete('cascade');
            $table->foreign('rid')->references('role_id')->on('roles')->onDelete('cascade');
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
        //
        Schema::dropIfExists('users_roles');
    }
}
