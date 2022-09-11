<?php

use App\Transaction;
use Illuminate\Database\Seeder;

class TransactionClosedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $m = factory(Transaction::class, 300)->create();
        // print($m);
        // print("\n");
    }
}
