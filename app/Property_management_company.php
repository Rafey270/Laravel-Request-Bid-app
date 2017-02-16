<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property_management_company extends Model
{
    protected $fillable= [
        'management_company',
        'management_company_street',
        'management_company_city',
        'management_company_state',
        'management_company_zip',
        'property_id'
    ];
}
