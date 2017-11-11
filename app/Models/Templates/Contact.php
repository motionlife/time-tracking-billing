<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Outreferrer;

class Contact extends Model
{
    protected $guarded = [];

    //Get the owner of this contact if it's a client
    public function client()
    {
        return $this->belongsTo(Client::class, 'cc_id')->withDefault([
            'name' => 'Not_A_Client'
        ]);
    }

    //Get the owner of this contact if it's a consultant
    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'cc_id')->withDefault([
            'first_name' => 'Not_A_Consultant',
            'last_name' => 'Not_A_Consultant'
        ]);
    }

    //Get the owner of this contact if it's a outsider referrer
    public function outreferrer()
    {
        return $this->belongsTo(Outreferrer::class, 'cc_id')->withDefault([
            'first_name' => 'Not_A_Outside_Referrer',
            'last_name' => 'Not_A_Outside_Referrer'
        ]);
    }
}
