<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRolesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->string('rid');
            $table->string("pid");
            $table->boolean("active")->default(false);
            $table->foreign('rid')->references('role_id')->on('roles')->onDelete('cascade');
            $table->foreign('pid')->references('permission_id')->on('permissions')->onDelete('cascade');
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
    }
}
