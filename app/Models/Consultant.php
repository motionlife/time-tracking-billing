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
}
