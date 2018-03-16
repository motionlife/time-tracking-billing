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

    public static function recognized($strict = false)
    {
        return self::all()->filter(function ($consultant) use ($strict) {
            return $strict ? $consultant->user->isVerified() : !$consultant->user->unRecognized();
        })->sortBy('first_name');
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

    //all the engagements he has ever served as closer
    public function close_engagements()
    {
        return $this->hasMany(Engagement::class, 'closer_id');
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
            $arrangement = Arrangement::withTrashed()->where('id', $aid)->first();
            $engagement = $arrangement->engagement()->withTrashed()->first();
            return ['ename' => '[' . $engagement->client->name . ']' . $engagement->name,
                'elead' => $engagement->leader->fullname(),
                'position' => $arrangement->position->name,
                'bhours' => $group->sum('billable_hours'),
                'nbhours' => $group->sum('non_billable_hours'),
                'brate' => $engagement->isHourlyBilling() ? $arrangement->billing_rate : 0,
                'prate' => $engagement->isHourlyBilling() ? $arrangement->billing_rate * (1 - $arrangement->firm_share) : $arrangement->pay_rate,
                'hourlyPay' => $group->sum(function ($hour) {
                    return $hour->earned();
                }),
                'expense' => 0, 'bizDevShare' => 0, 'bizDevIncome' => 0, 'closingShare' => 0, 'closings' => 0];
        });
        foreach ($expenseByArrangement as $aid => $expense) {
            if ($expense) {
                $payRow = $payByArrangement->get($aid);
                if (empty($payRow)) {
                    $arrangement = Arrangement::withTrashed()->where('id', $aid)->first();
                    $engagement = $arrangement->engagement()->withTrashed()->first();
                    $payRow = ['ename' => '[' . $engagement->client->name . ']' . $engagement->name,
                        'elead' => $engagement->leader->fullname(), 'position' => $arrangement->position->name,
                        'bhours' => 0, 'nbhours' => 0, 'brate' => 0, 'prate' => 0, 'hourlyPay' => 0, 'bizDevShare' => 0,
                        'bizDevIncome' => 0, 'closingShare' => 0, 'closings' => 0];
                }
                $payRow['expense'] = $expense;
                $payByArrangement->put($aid, $payRow);
            }
        }

        foreach ($this->getBuzDev($start, $end, $state, $eid) as $aid => $devPay) {
            $payRow = $payByArrangement->get($aid);
            if (empty($payRow)) {
                $payRow = ['ename' => $devPay['ename'], 'elead' => $devPay['elead'],
                    'position' => $devPay['position'], 'bhours' => 0, 'nbhours' => 0,
                    'brate' => 0, 'prate' => 0, 'hourlyPay' => 0, 'expense' => 0,
                    'closingShare' => 0, 'closings' => 0];
            }
            $payRow['bizDevShare'] = $devPay['bizDevShare'];
            $payRow['bizDevIncome'] = $devPay['bizDevIncome'];
            $payByArrangement->put($aid, $payRow);
        }

        foreach ($this->getClosings($start, $end, $state, $eid) as $aid => $closing) {
            $payRow = $payByArrangement->get($aid);
            if (empty($payRow)) {
                $payRow = ['ename' => $closing['ename'], 'elead' => $closing['elead'],
                    'position' => $closing['position'], 'bhours' => 0, 'nbhours' => 0,
                    'brate' => 0, 'prate' => 0, 'hourlyPay' => 0, 'expense' => 0,
                    'bizDevShare' => 0, 'bizDevIncome' => 0];
            }
            $payRow['closingShare'] = $closing['closingShare'];
            $payRow['closings'] = $closing['closings'];
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
                            'position' => $aids ? Arrangement::find($aids[0])->position->name : '***NOT IN***',
                            'bizDevShare' => $engagement->buz_dev_share,
                            'bizDevIncome' => $paid
                        ];
                    }
                }
            }
        }
        return collect($devPay);
    }

    private function getClosings($start = null, $end = null, $state = null, $eid = null)
    {
        $closings = [];
        $fake_aid = -10000;
        foreach ($this->close_engagements()->withTrashed()->get() as $engagement) {
            if (!$eid[0] || in_array($engagement->id, $eid)) {
                if (!$eid[0] || in_array($engagement->id, $eid)) {
                    if ($engagement->closer_share == 0) continue;
                    $paid = $engagement->incomeForCloser($start, $end, $state);
                    if ($paid) {
                        $aids = $this->findAidsFromEngagement($engagement);
                        $closings[$aids ? $aids[0] : ($fake_aid--)] = [
                            'ename' => '[' . $engagement->client->name . ']' . $engagement->name,
                            'elead' => $engagement->leader->fullname(),
                            'position' => $aids ? Arrangement::find($aids[0])->position->name : '***NOT IN***',
                            'closingShare' => $engagement->closer_share,
                            'closings' => $paid
                        ];
                    }
                }
            }
        }
        return collect($closings);
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
