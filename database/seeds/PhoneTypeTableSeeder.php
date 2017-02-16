<?php

use Illuminate\Database\Seeder;

use App\Phone_type;

class PhoneTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('Home','Office','Cell', 'Fax','Pager','Nextel','Other');
        foreach($data as $key =>$value){
            Phone_type::create([
                'name' =>$value,
            ]);
        }
    }
}
