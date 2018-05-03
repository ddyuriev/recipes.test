<?php

use Illuminate\Database\Seeder;
use \App\Models\Role;
use App\User;
use App\Http\Controllers\Utils\PasswordHasher;
use App\Http\Controllers\Utils\Misc;

class FirstStartTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->id = Role::ADMIN_ROLE;
        $role->name = 'admin';
        $role->save();

        $role = new Role();
        $role->id = Role::USER_ROLE;
        $role->name = 'user';
        $role->save();

        $hasher = new PasswordHasher();

        $user = new User();
        $user->login = 'admin';
        $user->password = $hasher->hash(1);
        $user->name = 'admin name';
        $user->address = 'admin addres';
        $user->image = 'admin image';
        $user->api_token = Misc::generateApiToken('admin');
        $user->token_created_at =date("Y-m-d H:i:s");
        $user->save();

        $user = new User();
        $user->login = 'user1';
        $user->password = $hasher->hash(1);
        $user->name = 'user1 name';
        $user->address = 'user1 addres';
        $user->image = 'user1 image';
        $user->api_token = Misc::generateApiToken('user1');
        $user->token_created_at = date("Y-m-d H:i:s");
        $user->save();

        DB::table('role_user')->insert([
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 2, 'role_id' => 2]
        ]);

    }
}
