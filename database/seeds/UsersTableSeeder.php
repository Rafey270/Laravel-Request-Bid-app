<?php

use Illuminate\Database\Seeder;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified' => '1',
        ]);
        $faker = Faker\Factory::create();
        $password = \Hash::make('password');
        for ($i = 0; $i <= 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->name.'@gmail.com',
                'password' => bcrypt('password'),
                'email_verified' => '1',
            ]);
        }
    }
}
