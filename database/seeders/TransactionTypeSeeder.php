<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_types')->insert([
            [
                "trans_desc" => "PURCHASE"
            ],
            [
                "trans_desc" => "TRANSFER"
            ],
            [
                "trans_desc" => "STOCK OUT"
            ],
            [
                "trans_desc" => "REMOVED"
            ],
            [
                "trans_desc" => "ADD"
            ],
            [
                "trans_desc" => "MOVEMENT"
            ]
        ]);
    }
}
