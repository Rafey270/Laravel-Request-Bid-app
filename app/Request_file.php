<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;

use App\File_type;
class Request_file extends Model
{
    protected $fillable = [
        'file_type',
        'description',
        'file_name',
        'mine',
        'file_change_name',
        'request_id',
        'creator_id',
    ];

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function fileType(){
        return $this->belongsTo(File_type::class, 'file_type');
    }

}
