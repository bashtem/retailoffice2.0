<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QtyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('qty_types')->insert([
            [
                "qty_desc" => "CARTON"
            ],
            [
                "qty_desc" => "KG"
            ],
            
        ]);
    }
}
