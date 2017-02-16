<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Contact_type;

class Property_management_company_contact extends Model
{
    protected $fillable = [
        'contact_type_id',
        'first_name',
        'last_name',
        'email',
        'property_id',
        'property_company_id'
    ];


    public function property_contact_type(){
        return $this->belongsTo(Contact_type::class, 'contact_type_id');
    }
}
