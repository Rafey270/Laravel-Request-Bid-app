<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
class Request extends Model
{
    protected $fillable = [
        'requester_name',
        'requester_phone',
        'requester_fax',
        'scope',
        'details',
        'source_id',
        'bid_statuses_id',
        'estimator_id',
        'creator_id',
        'assign_id',
        'editor_id',
        'assign_date',
        'property_id',
        'property_phone_id',
        'property_contact_id',
        'property_contact_phone_id',
        'property_company_id',
        'property_company_phone_id',
        'property_company_contact_id',
    ];

    public function assignBy(){
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function estimator(){
        return $this->belongsTo(User::class, 'estimator_id');
    }
}
