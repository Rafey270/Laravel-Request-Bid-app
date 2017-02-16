<?php

use Illuminate\Database\Seeder;

use App\Mail_to_type;

class MailToTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('Yes','No','Returned', 'Deleted','Remove');
        foreach($data as $key =>$value){
            Mail_to_type::create([
                'name' =>$value,
            ]);
        }
    }
}
