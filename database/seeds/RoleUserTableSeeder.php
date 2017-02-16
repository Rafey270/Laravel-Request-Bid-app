<?php

use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_user')->insert([
            [
                'id' => 1,
                'role_id' => 1,
                'user_id' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        ]);

        $users = App\User::all();
        foreach($users as $key =>$user){
            if($key != 0){
                DB::table('role_user')->insert([
                    'id' => $key +1,
                    'user_id' =>$user->id,
                    'role_id' => 2,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }

        }
    }
}
