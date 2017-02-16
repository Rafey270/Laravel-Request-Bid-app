<?php

use Illuminate\Database\Seeder;

use App\Bid_template;
class BidTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bid_template::create([
            'name' =>'Chesapeake Finishing',
        ]);
        Bid_template::create([
            'name' =>'Chesapeake Finishing, CA',
        ]);
        Bid_template::create([
            'name' =>'Chesapeake Finishing, OR',
        ]);
        Bid_template::create([
            'name' =>'CFI Group-US',
        ]);
        Bid_template::create([
            'name' =>'Chesapeake Group, LLC - CA',
        ]);
    }
}
