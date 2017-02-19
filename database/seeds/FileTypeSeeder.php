<?php

use Illuminate\Database\Seeder;

use App\File_type;
class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('Draft Bid','Approved Bid','Image','Other');
        foreach($data as $key =>$value){
            File_type::create([
                'name' =>$value,
            ]);
        }
    }
}
