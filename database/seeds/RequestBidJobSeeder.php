<?php

use Illuminate\Database\Seeder;

use App\Request_bid_job;

class RequestBidJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('Request','Bid','Job');
        foreach($data as $key =>$value){
            Request_bid_job::create([
                'name' =>$value,
            ]);
        }
    }
}
