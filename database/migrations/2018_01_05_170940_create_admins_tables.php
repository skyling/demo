<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTables extends Migration
{
    /**
     * 管理员表
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 50)->unique()->comment('邮箱');
            $table->string('username')->unique()->commnet('用户名称');
            $table->string('password')->comment('密码');
            $table->tinyInteger('status')->default(0)->comment('状态 1:启用 0:禁用');
            $table->string('shop_id')->default('')->comment('店铺权限ID,多个逗号隔开');
            $table->timestamps();
        });

        Schema::create('admin_has_roles', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('admin_id')->unsigned();

            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');

            $table->primary(['admin_id', 'role_id']);

        });


        // 登录日志表
        Schema::create('admin_login_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->comment('管理员ID');
            $table->bigInteger('ip')->comment('登录IP');
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
        Schema::dropIfExists('admin_has_roles');
        Schema::dropIfExists('admin_login_logs');
        Schema::dropIfExists('admins');
    }
}
