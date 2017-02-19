<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;

class Request_line_item extends Model
{
    protected $fillable = [
        'line_item_description',
        'lump_sum',
        'order',
        'option_flag',
        'creator_id',
        'request_id'
    ];
    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }
}
