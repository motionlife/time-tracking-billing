<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\Models\Templates\Industry;
use newlifecfo\User;

class Client extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
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
        return $this->belongsTo(Contact::class);
    }

    //Get the consultant who developed this client
    public function dev_by_consultant()
    {
        return $this->belongsTo(Consultant::class, 'buz_dev_person_id')
            ->withDefault([
                'first_name' => 'New Life',
                'last_name' => 'CFO'
            ]);
    }

    //Get the outside referrer who developed this client
    public function outreferrer()
    {
        return $this->belongsTo(Outreferrer::class)
            ->withDefault([
                'first_name' => 'N/A',
                'last_name' => ''
            ]);
    }

    public function whoDevelopedMe()
    {
        if ($this->outreferrer->first_name == 'N/A') {
            return $this->dev_by_consultant->fullname();
        } else {
            return $this->outreferrer->fullname() . ' (O.R.)';
        }
    }

    //get all the available revenues
    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    //client belong to which industry
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    //get all the engagements of this client
    public function engagements()
    {
        return $this->hasMany(Engagement::class);
    }

    public function laborBills($start = '1970-01-01', $end = '2038-01-19')
    {
        //only those engagements that bill client hourly
        $total = 0;
        foreach ($this->engagements as $engagement) {
            $total += $engagement->clientLaborBills($start, $end);
        }
        return $total;
    }

    public function expenseBills($start = '1970-01-01', $end = '2038-01-19')
    {
        $total = 0;
        foreach ($this->engagements as $engagement) {
            $total += $engagement->clientExpenseBills($start, $end);
        }
        return $total;
    }
}
