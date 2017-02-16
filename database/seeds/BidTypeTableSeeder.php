<?php

use Illuminate\Database\Seeder;
use App\Bid_type;

class BidTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bid_type::create([
            'name' =>'Standard',
        ]);
        Bid_type::create([
            'name' =>'Custom',
        ]);
    }
}
