<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\Models\Templates\Industry;
use newlifecfo\User;

class Client extends Model
{
    protected $guarded = [];

    //Get the corresponding system user of this client
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'first_name' => 'Unregistered',
            'last_name' => 'Unregistered',
            'priority' => 0
        ]);
    }

    //Get the contact info
    public function contact()
    {
        return $this->hasOne(Contact::class, 'cc_id');
    }

    //Get the consultant who developed this client
    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'buz_dev_person_id')
            ->withDefault([
                'first_name' => 'New Life CFO',
                'last_name' => 'New Life CFO'
            ]);
    }

    //Get the outside referrer who developed this client
    public function outreferrer()
    {
        return $this->belongsTo(Outreferrer::class)
            ->withDefault([
                'first_name' => 'N/A',
                'last_name' => 'N/A'
            ]);
    }

    //get all the available revenues
    public function revenues()
    {
        $this->hasMany(Revenue::class);
    }

    //client belong to which industry
    public function industry()
    {
        return $this->belongsTo(Industry::class,'industry_id');
    }
}
