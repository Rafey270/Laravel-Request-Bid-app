<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
class Request_comment extends Model
{
    public function creator(){
        return $this->belongsTo(User::class,'creator_id');
    }
}
