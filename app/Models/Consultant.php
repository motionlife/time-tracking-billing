<?php

namespace newlifecfo\Models;

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
        return $this->belongsTo(Contact::class);
    }

    //all the developed clients
    public function dev_clients()
    {
        return $this->hasMany(Client::class, 'buz_dev_person_id');
    }

    //all the engagements he has ever leaded
    public function lead_engagements()
    {
        return $this->hasMany(Engagement::class, 'leader_id');
    }

    public function my_lead_engagements($start = null, $cid = null)
    {
        $filered = $this->lead_engagements()->where('start_date', '>=', $start ?: '1970-01-01')->orderBy('created_at', 'DESC');
        return isset($cid) ? $filered->where('client_id', $cid)->get() : $filered->get();
    }

    //all the arrangements he's attended
    public function arrangements()
    {
        return $this->hasMany(Arrangement::class);
    }

    public function recentHourOrExpenseReports($start = null, $end = null, $eid = null, $hour = true)
    {
        $resource = $hour ? Hour::class : Expense::class;
        $aids = isset($eid) ? $this->arrangements()->where('engagement_id', $eid)->pluck('id') :
            $this->arrangements()->pluck('id');
        //isset($eid) ? Engagement::find($eid)->arrangements->pluck('id') :
        if ($start || $end)
            return $resource::whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19'])
                ->whereIn('arrangement_id', $aids)->orderByRaw('report_date DESC, created_at DESC')->get();
        else
            return $resource::whereIn('arrangement_id', $aids)->orderByRaw('report_date DESC, created_at DESC')->get();
    }

    public function justCreatedHourReports($start = null, $end = null, $amount=null)
    {
        return Hour::whereBetween('created_at', [$start ?: '1970-01-01', $end ?: '2038-01-19'])
            ->whereIn('arrangement_id', $this->arrangements()->pluck('id'))->orderBy('created_at', 'DESC')->take($amount)->get();
    }

    public function EngagementByClient()
    {
        //what if arr's eng had been deleted Should add stuatus to arrangement
        //todo: Alter Arrangement table add status column, use it to deal with the case where it's engagement hab been deleted
        $eids = Engagement::all()->pluck('id');
        return $this->arrangements->whereIn('engagement_id',$eids)->groupBy('engagement_id')
            ->mapToGroups(function ($item, $key) {
                $eng = Engagement::find($key);
                $cid = $eng->client->id;
                return [$cid => [$eng->id, $eng->name]];
            });
    }

    public function myEngagements($start = null, $cid = null)
    {
        $eids = $this->arrangements()->pluck('engagement_id');
        return isset($cid) ? Engagement::whereIn('id', $eids)->where('start_date', '>=', $start ?: '1970-01-01')->where('client_id', $cid)->orderBy('created_at', 'DESC')->get() :
            Engagement::whereIn('id', $eids)->where('start_date', '>=', $start ?: '1970-01-01')->orderBy('created_at', 'DESC')->get();
    }

    public function getArrInfoByEid($eid)
    {
        return $this->arrangements()->where('engagement_id', $eid)->get()->map(function ($arr) {
            return ['position'=>$arr->position,'br'=>$arr->billing_rate,'fs'=>$arr->firm_share];
        });
    }

    public function getArrangementByEidPid($eid, $pid = null)
    {
        if (isset($pid))
            return $this->arrangements()->where([['engagement_id', '=', $eid], ['position_id', '=', $pid]])->first();
        return $this->arrangements()->where('engagement_id', $eid)->first();
    }
}
