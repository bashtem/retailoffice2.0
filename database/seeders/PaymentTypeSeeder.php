 <?php
 

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_types')->insert([
            [
                "payment_desc" => "CASH"
            ],
            [
                "payment_desc" => "CREDIT"
            ],
            [
                "payment_desc" => "BANK TRANSFER"
            ],
            [
                "payment_desc" => "CHEQUE"
            ],
            [
                "payment_desc" => "POS"
            ]
           
        ]);
    }
}
