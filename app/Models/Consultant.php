<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\User;

class Consultant extends Model
{
    protected $guarded = [];

    //Get the corresponding system user of this consultant
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'first_name' => 'Unregistered',
            'last_name' => 'Unregistered',
            'priority' => 0
        ]);
    }

    //Get the contact
    public function contact()
    {
        return $this->hasOne(Contact::class,'cc_id');
    }

    //all the developed clients
    public function clients()
    {
        return $this->hasMany(Client::class,'buz_dev_person_id');
    }
}
