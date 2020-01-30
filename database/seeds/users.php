<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            "email" => "admin@admin.com",
            "first_name" => "Abdullah",
            "last_name" => "Shakir",
            "username" => "admin",
            "password" =>  Hash::make("asdfasdf"),
            "is_admin" => 1
        ]);
    }
}
