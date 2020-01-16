<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            "email" => "ashakir@knackforte.com",
            "first_name" => "Abdullah",
            "last_name" => "Shakir",
            "password" =>  Hash::make("123456789")
        ]);
    }
}
