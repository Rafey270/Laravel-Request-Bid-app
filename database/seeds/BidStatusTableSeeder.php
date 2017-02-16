<?php

use Illuminate\Database\Seeder;

use App\Bid_status;

class BidStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bid_status::create([
            'bid_status_name' =>'create',
        ]);
        Bid_status::create([
            'bid_status_name' =>'pending',
        ]);
        Bid_status::create([
            'bid_status_name' =>'award',
        ]);
    }
}
