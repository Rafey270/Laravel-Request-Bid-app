<?php

use Illuminate\Database\Seeder;
use App\Contact_type;

class ContactTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('Architect/Designer','Property Manager','Site Manager','Maintenance Manager','Owner','Other');
        foreach($data as $key =>$value){
            Contact_type::create([
                'name' =>$value,
            ]);
        }
    }
}
