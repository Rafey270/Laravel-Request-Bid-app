<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Bid_type;
use App\Bid_template;
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
        'approved_id',
        'assign_id',
        'editor_id',
        'assign_date',
        'billing_date',
        'paid_date',
        'property_id',
        'bid_template_id',
        'bid_type_id',
        'request_bid_job_id',
        'approved_flag',
        'archive_bid',
        'due_date',
    ];

    public function assignBy(){
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function estimator(){
        return $this->belongsTo(User::class, 'estimator_id');
    }
    public function bidType(){
        return $this->belongsTo(Bid_type::class, 'bid_type_id');
    }
    public function bidTemplate(){
        return $this->belongsTo(Bid_template::class, 'bid_template_id');
    }
}
