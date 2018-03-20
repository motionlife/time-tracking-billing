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

    //Obsoleted! Based on customer's description, outsider referrer is not a business developer
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

    public function setRevenue($year, $rev, $ebi)
    {
        Revenue::updateOrCreate(['client_id' => $this->id, 'year' => $year], ['revenue' => $rev, 'ebit' => $ebi]);
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

    public function engagementBill($start = null, $end = null, $state = null, $eid = null)
    {
        $hours = Hour::reported($start, $end, $eid, null, $state, $this)->filter(function ($value) {
            return $value->rate_type == 0;
        });

        $sumHours = $hours->reduce(function ($carry, $hour) {
            return [$carry[0] + $hour->billable_hours, $carry[1] + $hour->non_billable_hours, $carry[2] + $hour->billClient()];
        });
        $sumHours = [$sumHours[0] ?: 0, $sumHours[1] ?: 0, $sumHours[2] ?: 0];
        $NonHourlyEngagements = collect();
        foreach ($this->engagements()->withTrashed()->get() as $engagement) {
            if (!$eid[0] || in_array($engagement->id, $eid)) {
                $billed = $engagement->NonHourBilling($start, $end, $state);
                if (!$engagement->isHourlyBilling()) {
                    $sumHours[2] += $billed;
                    $NonHourlyEngagements->push([$engagement, $billed]);
                }
            }
        }

        return [$sumHours[2], $hours, $sumHours[0], $sumHours[1], 'NonHourlyEngagement' => $NonHourlyEngagements];
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

    public function constructBills($start = null, $end = null, $state = null, $eid = null)
    {
        //Hourly Eng. Bill
        $billByArrangement = Hour::reported($start, $end, $eid, null, $state, $this)->filter(function ($hp) {
            return $hp->rate_type == 0;
        });
        if (!$billByArrangement->count()) $billByArrangement = collect();//fix Illuminate\Database\Eloquent\Collection bugs
        $billByArrangement = $billByArrangement->groupBy('arrangement_id')->map(function ($group, $aid) {
            $arrangement = Arrangement::withTrashed()->where('id', $aid)->first();
            $engagement = $arrangement->engagement()->withTrashed()->first();
            $row = ['eid' => $engagement->id, 'ename' => $engagement->name,
                'position' => $arrangement->position->name,
                'consultant' => $arrangement->consultant->fullname(),
                'bhours' => $group->sum('billable_hours'),
                'nbhours' => $group->sum('non_billable_hours'),
                'brate' => $arrangement->billing_rate,
                'bType' => 'Hourly',
                'engBill' => $group->sum(function ($hour) {
                    return $hour->billClient();
                }), 'expBill' => 0];
            return $row;
        });

        //Non-hourly Eng. Bill
        $NonHourlyEngagements = collect();
        foreach ($this->engagements()->withTrashed()->get() as $engagement) {
            if (!$eid[0] || in_array($engagement->id, $eid)) {
                $billed = $engagement->NonHourBilling($start, $end, $state);
                if (!$engagement->isHourlyBilling()) {
                    $NonHourlyEngagements->push([
                        'eid' => $engagement->id,
                        'ename' => $engagement->name,
                        'position' => 'Multiple',
                        'consultant' => 'Multiple',
                        'bhours' => null,
                        'nbhours' => null,
                        'brate' => null,
                        'bType' => $engagement->clientBilledType(),
                        'engBill' => $billed,
                        'expBill' => 0,
                    ]);
                }
            }
        }

        //Expense Bill
        $expenseByArrangement = Expense::reported($start, $end, $eid, null, $state, $this)
            ->groupBy('arrangement_id')
            ->map(function ($group) {
                return $group->sum(function ($expense) {
                    return $expense->total();
                });
            });
        foreach ($expenseByArrangement as $aid => $expense) {
            if ($expense) {
                $billRow = $billByArrangement->get($aid);
                if (empty($billRow)) {
                    $arrangement = Arrangement::withTrashed()->where('id', $aid)->first();
                    $engagement = $arrangement->engagement()->withTrashed()->first();
                    $billRow = ['eid' => $engagement->id, 'ename' => $engagement->name,
                        'position' => $arrangement->position->name,
                        'consultant' => $arrangement->consultant->fullname(),
                        'bhours' => 0, 'nbhours' => 0, 'brate' => $engagement->isHourlyBilling() ? $arrangement->billing_rate : 0,
                        'bType' => null, 'engBill' => 0];
                }
                $billRow['expBill'] = $expense;
                $billByArrangement->put($aid, $billRow);
            }
        }
        return $billByArrangement->values()->merge($NonHourlyEngagements)->groupBy('eid');
    }
}
