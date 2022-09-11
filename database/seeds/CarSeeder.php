<?php

use Illuminate\Database\Seeder;
use App\Car;
class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $m = factory(Car::class, 20)->create();
        //print($m);print ("\n");
    }
}
