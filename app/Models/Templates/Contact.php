<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Outreferrer;

class Contact extends Model
{
    protected $guarded = [];

    //Get the owner of this contact
    public function owner()
    {
        return $this->hasOne(Consultant::class) ?: $this->hasOne(Client::class) ?: $this->hasOne(Outreferrer::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class)->withDefault();
    }
}
