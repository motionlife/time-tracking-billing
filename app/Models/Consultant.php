<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\User;

class Consultant extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    public function fullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function recognized()
    {
        return self::all()->filter(function ($consultant) {
            return $consultant->user->isVerified();
        });
    }

    //Get the corresponding system user of this consultant
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

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    //all the developed clients
    public function dev_clients()
    {
        return $this->hasMany(Client::class, 'buz_dev_person_id');
    }

    //all the arrangements he's attended
    public function arrangements()
    {
        return $this->hasMany(Arrangement::class);
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

    //all the engagements he has ever leaded
    public function lead_engagements()
    {
        return $this->hasMany(Engagement::class, 'leader_id');
    }

    public function justCreatedHourReports($start = null, $end = null, $amount = null)
    {
        return Hour::whereBetween('created_at', [$start ?: '1970-01-01', $end ?: '2038-01-19'])
            ->whereIn('arrangement_id', $this->arrangements()->pluck('id'))->orderBy('created_at', 'DESC')->take($amount)->get();
    }

    public function getMyArrInfoByEid($eid)
    {
        return $this->arrangements()->where('engagement_id', $eid)->get()->map(function ($arr) {
            $eng = $arr->engagement;
            return ['position' => $arr->position, 'br' => $eng->paying_cycle == 0 ? $arr->billing_rate : $arr->pay_rate, 'fs' => $eng->paying_cycle == 0 ? $arr->firm_share : 0];
        });
    }

    public function getMyArrangementByEidPid($eid, $pid = null)
    {
        if (isset($pid))
            return $this->arrangements()->where([['engagement_id', '=', $eid], ['position_id', '=', $pid]])->first();
        return $this->arrangements()->where('engagement_id', $eid)->first();
    }

    //What is the best index to tell 'most frequent' input task? 2 factors: recent + count
    public function getRecentInputTask($amount = 5)
    {
        return $this->justCreatedHourReports(null, null, 70)->mapToGroups(function ($item, $key) {
            $arr = $item->arrangement;
            return [$arr->engagement_id . '-' . $arr->position_id . '-' . $item->task_id => 1 / (Carbon::now()->diffInMinutes($item->created_at) + 1)];
        })->sortByDesc(function ($item, $key) {
            $total = 0.0;
            foreach ($item as $delta) {
                $total += $delta;
            }
            return $total;
        })->take($amount);
    }

    //get the payroll for excel output
    public function getPayroll($start = null, $end = null, $state = null, $eid = null)
    {
        $hourReports = Hour::reported($start, $end, $eid, $this, $state);
        $expenseByArrangement = Expense::reported($start, $end, $eid, $this, $state)
            ->groupBy('arrangement_id')
            ->map(function ($group) {
                return $group->sum(function ($expense) {
                    return $expense->payConsultant();
                });
            });
        $payByArrangement = $hourReports->groupBy('arrangement_id')->map(function ($group, $aid) {
            $row = [];
            $arrangement = Arrangement::find($aid);
            $engagement = $arrangement->engagement;
            $row['ename'] = '[' . $engagement->client->name . ']' . $engagement->name;
            $row['elead'] = $engagement->leader->fullname();
            $row['position'] = $arrangement->position->name;
            $row['bhours'] = $group->sum('billable_hours');
            $row['nbhours'] = $group->sum('non_billable_hours');
            $row['brate'] = $engagement->isHourlyBilling() ? $arrangement->billing_rate : 0;
            $row['prate'] = $engagement->isHourlyBilling() ? $arrangement->billing_rate * (1 - $arrangement->firm_share) : $arrangement->pay_rate;
            $row['hourlyPay'] = $group->sum(function ($hour) {
                return $hour->earned();
            });
            $row['expense'] = 0;
            $row['bizDevShare'] = 0;
            $row['bizDevIncome'] = 0;
            return $row;
        });
        foreach ($expenseByArrangement as $aid => $expense) {
            if ($expense) {
                $payRow = $payByArrangement->get($aid);
                if (empty($payRow)) {
                    $arrangement = Arrangement::find($aid);
                    $engagement = $arrangement->engagement;
                    $payRow['ename'] = '[' . $engagement->client->name . ']' . $engagement->name;
                    $payRow['elead'] = $engagement->leader->fullname();
                    $payRow['position'] = $arrangement->position->name;
                    $payRow['bhours'] = 0;
                    $payRow['nbhours'] = 0;
                    $payRow['brate'] = 0;
                    $payRow['prate'] = 0;
                    $payRow['hourlyPay'] = 0;
                    $payRow['bizDevShare'] = 0;
                    $payRow['bizDevIncome'] = 0;
                }
                $payRow['expense'] = $expense;
                $payByArrangement->put($aid, $payRow);
            }
        }

        foreach ($this->getBuzDev($start, $end, $state, $eid) as $aid => $devPay) {
            $payRow = $payByArrangement->get($aid);
            if (empty($payRow)) {
                $payRow['ename'] = $devPay['ename'];
                $payRow['elead'] = $devPay['elead'];
                $payRow['position'] = $devPay['position'];
                $payRow['bhours'] = 0;
                $payRow['nbhours'] = 0;
                $payRow['brate'] = 0;
                $payRow['prate'] = 0;
                $payRow['hourlyPay'] = 0;
                $payRow['expense'] = 0 ;
            }
            $payRow['bizDevShare'] = $devPay['bizDevShare'];
            $payRow['bizDevIncome'] = $devPay['bizDevIncome'];
            $payByArrangement->put($aid, $payRow);
        }
        return $payByArrangement;
    }

    public function getBuzDev($start = null, $end = null, $state = null, $eid = null)
    {
        $devPay = [];
        $fake_aid = -1;
        foreach ($this->dev_clients()->withTrashed()->get() as $dev_client) {
            foreach ($dev_client->engagements()->withTrashed()->get() as $engagement) {
                if (!$eid[0] || in_array($engagement->id, $eid)) {
                    if ($engagement->buz_dev_share == 0) continue;
                    $paid = $engagement->incomeForBuzDev($start, $end, $state);
                    if ($paid) {
                        $aids = $this->findAidsFromEngagement($engagement);
                        $devPay[$aids ? $aids[0] : ($fake_aid--)] = [
                            'ename' => '[' . $engagement->client->name . ']' . $engagement->name,
                            'elead' => $engagement->leader->fullname(),
                            'position' => $aids ? Arrangement::find($aids[0])->position->name : '',
                            'bizDevShare' => $engagement->buz_dev_share,
                            'bizDevIncome' => $paid
                        ];
                    }
                }
            }
        }
        return collect($devPay);
    }

    private function findAidsFromEngagement($engagement)
    {
        $aids = [];
        $conid = $this->id;
        foreach ($engagement->arrangements as $arrangement) {
            if ($arrangement->consultant_id == $conid) array_push($aids, $arrangement->id);
        }
        return $aids;
    }
}
