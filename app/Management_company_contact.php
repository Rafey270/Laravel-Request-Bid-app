<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Management_company_contact extends Model
{
    protected $fillable = [
        'contact_type_id',
        'first_name',
        'last_name',
        'email',
        'management_company_id',
    ];


    public function property_contact_type(){
        return $this->belongsTo(Contact_type::class, 'contact_type_id');
    }

    public function management_company(){
        return $this->belongsTo(Management_company::class, 'management_company_id');
    }
}
