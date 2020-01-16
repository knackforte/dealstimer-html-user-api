<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permission = new Permission();
        $permission->name = "user_management";
        $permission->display_name = "User Management";
        $permission->description = "See list of users and perform CRUD operations";
        $permission->save();
    }
}
