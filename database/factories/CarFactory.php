<?php
use App\Car;
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

$factory->define(Car::class, function (Faker $faker) {

    static $counter ;
    $counter++;
    $color = $this->faker->colorName();
    $manufacture = App\Manufacture::inRandomOrder()->first();
    $name = substr($manufacture->name, 0, 3) . "-" . substr($color, 0, 3) . "-" . str_pad($counter, 3, '0', STR_PAD_LEFT);
    $name = strtoupper($name);
    $license_number = $this->faker->numerify("B #### ") . strtoupper($this->faker->lexify('???'));
    $year = $this->faker->NumberBetween(1998, 2022);
    $price = $this->faker->randomElement([125000, 150000, 300000, 170000]);

    return [
        'manufacture_id' => $manufacture->id,
        'name' => $name,
        'license_number' => $license_number,
        'color' => $color,
        'status' => "tersedia",
        'year' => $year,
        'price' => $price,
        'penalty' => $price * 0.05,
    ];
});
