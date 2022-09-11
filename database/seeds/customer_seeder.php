<?php

use Illuminate\Database\Seeder;

class customer_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create("id_ID");

        for ($i = 0; $i < 150; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $dob = $faker->dateTimeBetween('-50 years', '-20 years');
            $nik = $faker->nik($gender, $dob);
            App\Customer::create([
                'name' => $faker->name,
                'nik' => $nik,
                'address' => $faker->address,
                'sex' => $gender == "male" ? "laki-laki" : "perempuan",
                'slug' => str_slug($faker->name),
                'phone_number' => $faker->phoneNumber,
                'email' => $faker->email,
            ]);
        }
    }
}
