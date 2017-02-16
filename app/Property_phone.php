<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Phone_type;
class Property_phone extends Model
{
    protected $fillable = [
        'phone_type_id',
        'area_code',
        'phone_number',
        'phone_ext',
        'property_id',
    ];
    public function property_phone_type(){
        return $this->belongsTo(Phone_type::class, 'phone_type_id');
    }
}
