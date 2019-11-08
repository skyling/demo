<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Demo\Model\Admin::class, 1)->create([
            'username' => '超级管理员',
            'email' => 'admin@admin.com',
            'password' => '123456',
            'status' => \Demo\Model\Admin::STATUS_ON,
        ]);
    }
}
