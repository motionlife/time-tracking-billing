<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Task;

class Hour extends Model
{
    protected $guarded = [];

    //to which arrangement the hour reported
    public function arrangement()
    {
        return $this->belongsTo(Arrangement::class);
    }

    //what task does it report
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function report_day_in_last_week($few)
    {
        $lastweek = Carbon::parse('2017-07-31')->addDays((1 - $few) * 7)->startOfDay();
        return $lastweek->diffInDays(Carbon::parse($this->report_date), false);

    }

    public function contributed_hours_last_week(array &$data, $rate)
    {
        $diff = $this->report_day_in_last_week(1);
        if (0 <= $diff && $diff <= 6) {
            $data['weekly'][$diff]['bh'] += $this->billable_hours;
            $data['weekly'][$diff]['nbh'] += $this->non_billable_hours;
            $data['weekly'][$diff]['earn'] += $this->billable_hours * $rate;
            $eid = $this->arrangement->engagement->id;
            if (!isset($data['eids'][$eid])) {
                $data['eids'][$eid] = 1;
            } else {
                $data['eids'][$eid] += 1;
            }
        }
    }
}
