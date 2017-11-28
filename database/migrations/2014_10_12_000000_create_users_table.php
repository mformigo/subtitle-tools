<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        User::forceCreate([
            'name'     => 'Sjors',
            'username' => 'Sjors',
            'password' => '$2y$10$w2qaoj4Qrh4c8aEY5tobP.XptWGy/cMbmFoHvK/LD9gLenZHf5hMq',
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
