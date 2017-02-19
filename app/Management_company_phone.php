<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Management_company;

use App\Phone;

class Management_company_phone extends Model
{
    protected $fillable = [
        'management_company_id',
        'phone_id'
    ];

    public function management_company(){
        return $this->belongsTo(Management_company::class, 'management_company_id');
    }

    public function phone(){
        return $this->belongsTo(Phone::class, 'phone_id');
    }
}
