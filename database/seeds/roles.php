<?php

use Illuminate\Database\Seeder;

use App\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $role = new Role();
        $role->name = "admin";
        $role->display_name = "Admin";
        $role->description = "Admin will have access to all the functionalities.";
        $role->save();

        $role2 = new Role();
        $role2->name = "vendor";
        $role2->display_name = "Vendor";
        $role2->description = "Vendor will add their products.";
        $role2->save();

        $role = new Role();
        $role->name = "user";
        $role->display_name = "User";
        $role->description = "User will have access to browse.";
        $role->save();
    }
}
