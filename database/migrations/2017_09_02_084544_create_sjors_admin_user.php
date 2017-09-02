<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

class CreateSjorsAdminUser extends Migration
{
    public function up()
    {
        $user = new User();

        $user->name = 'Sjors';

        $user->username = 'Sjors';

        $user->password = '$2y$10$w2qaoj4Qrh4c8aEY5tobP.XptWGy/cMbmFoHvK/LD9gLenZHf5hMq';

        $user->save();
    }

    public function down()
    {
        $user = User::query()->where('username', 'Sjors');

        if($user->count() > 0) {
            $user->delete();
        }
    }
}
