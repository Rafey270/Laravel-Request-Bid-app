<?php

use Illuminate\Database\Seeder;
use App\Source;
class SourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i <= 4; $i++) {
            source::create([
                'source_name' =>$faker->name,
            ]);
        }
    }
}
