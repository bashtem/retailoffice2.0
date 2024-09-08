<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert([
            [
                "role_desc" => "Manager",
                "role_level" => "ADMIN"
            ],
            [
                "role_desc" => "Sales Attendant",
                "role_level" => "SALES"
            ],
            [
                "role_desc" => "Stock Keeper",
                "role_level" => "STOCK"
            ],
            
        ]);
    }
}
