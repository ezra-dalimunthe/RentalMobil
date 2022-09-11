<?php
use App\Transaction;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
 */

$factory->define(Transaction::class, function (Faker $faker) {

    $car = \App\Car::inRandomOrder()->first();
    $customer = \App\Customer::inRandomOrder()->first();
    $transdate = $this->faker->dateTimeBetween("-8 months", "-1 day");
    $transcounter = \App\Transaction::whereDate("rent_date", $transdate->format("Y-m-d"))->count() + 1;
    $invoice_no = "TRX-" . $transdate->format("dmY") . "-" . str_pad($transcounter, 5, '0', STR_PAD_LEFT);
//create transaction in pass, all status are 'selesai';
    $rent_date = clone $transdate;
    $back_date = clone $transdate;
    $back_date->add(new \DateInterval("P" . $this->faker->numberBetween(1, 10) . "D"));
    $return_date = clone $back_date;

    $normal_interval = $transdate->diff($back_date);
    if ($normal_interval->days > 2) {
        $nDays = $this->faker->numberBetween(-1, 3);
        if ($nDays < 0) {
            $return_date->modify($nDays . " days");
        } elseif ($nDays > 0) {
            $return_date->modify($nDays . " days");
        }
    }

    $price = $car->price;
    $normal_interval = $transdate->diff($back_date);
    $amount = $normal_interval->days * $car->price;
    $penalty_interval = $back_date->diff($return_date);
    $penalty = 0;
    if ($penalty_interval->invert == 0) {
        $penalty = $penalty_interval->days * ($car->penalty + $car->price);
    }

    return [
        "customer_id" => $customer->id,
        "car_id" => $car->id,
        "invoice_no" => $invoice_no,
        "rent_date" => $rent_date->format("Y-m-d"),
        "back_date" => $back_date->format("Y-m-d"),
        "return_date" => $return_date->format("Y-m-d"),
        "price" => $price,
        "amount" => $amount,
        "penalty" => $penalty,
        "status" => "selesai",

    ];
});
