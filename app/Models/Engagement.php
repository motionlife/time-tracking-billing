<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Engagement extends Model
{
    protected $guarded = [];

    //get all the arrangements that attached to this engagement
    public function arrangements()
    {
        return $this->hasMany(Arrangement::class);
    }

    //get the client who initiated the engagement
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    //get the leader(consultant) of the engagement
    public function leader()
    {
        return $this->belongsTo(Consultant::class);
    }
}
