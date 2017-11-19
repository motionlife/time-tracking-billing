<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\User;

class Consultant extends Model
{
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
        return $this->hasMany(Engagement::class);
    }

    //all the arrangements he's attended
    public function arrangements()
    {
        return $this->hasMany(Arrangement::class);
    }

    public function recentHourReports($num)
    {
        $aids = $this->arrangements->pluck('id');
        return Hour::all()->whereIn('arrangement_id', $aids)->sortByDesc('report_date')->take($num);
    }

    public function EngagementByClient()
    {
        return $this->arrangements->groupBy('engagement_id')
            ->mapToGroups(function ($item, $key) {
                $eng = Engagement::find($key);
                $cid = $eng->client->id;
                return [$cid => $eng->name];
            });
    }
}
