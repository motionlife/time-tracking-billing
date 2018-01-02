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
        return $this->belongsTo(Contact::class)->withDefault();
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
    public function setRevenue($year,$rev,$ebi)
    {
        Revenue::updateOrCreate(['client_id'=>$this->id,'year'=>$year],['revenue'=>$rev,'ebit'=>$ebi]);
    }
    public function getRevenue($year, $type)
    {
        $rev = $this->revenues->where('year', $year)->first();
        return $rev ? $rev->$type : null;
    }

    //client belong to which industry
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id')->withDefault();
    }

    //get all the engagements of this client
    public function engagements()
    {
        return $this->hasMany(Engagement::class);
    }

    public function hours()
    {
        return $this->hasMany(Hour::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function reports($class)
    {
        return $this->hasMany($class);
    }

    public function hourBill($start = null, $end = null, $state = null, $eid = null)
    {
        $hours = Hour::reported($start, $end, $eid, null, $state, $this);
        $sumHours = $hours->reduce(function ($carry, $hour) {
            return [$carry[0] + $hour->billable_hours, $carry[1] + $hour->non_billable_hours, $carry[2] + $hour->billClient()];
        });
        $sumHours = [$sumHours[0] ?: 0, $sumHours[1] ?: 0, $sumHours[2] ?: 0];
        foreach ($this->engagements()->withTrashed()->get() as $engagement) {
            if (!$eid[0] || in_array($engagement->id, $eid)) {
                $sumHours[2] += $engagement->NonHourBilling($start, $end, $state);
            }
        }

        return [$sumHours[2], $hours, $sumHours[0], $sumHours[1]];
    }

    public function expenseBill($start = null, $end = null, $state = null, $eid = null)
    {
        $total = 0;
        $expenses = Expense::reported($start, $end, $eid, null, $state, $this);
        foreach ($expenses as $expense) {
            $total += $expense->total();
        }
        return [$total, $expenses];
    }

    public function getEngagementIdName()
    {
        $pair = collect();
        foreach ($this->engagements as $engagement) {
            $pair->push([$engagement->id, $engagement->name]);
        }
        return [$this->id => $pair];
    }
}
