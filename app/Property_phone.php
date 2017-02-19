<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Phone_type;

use App\Property;

use App\Phone;
class Property_phone extends Model
{
    protected $fillable = [
        'phone_id',
        'property_id',
    ];

    public function property(){
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function phone(){
        return $this->belongsTo(Phone::class, 'phone_id');
    }
}
