<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use newlifecfo\Models\Templates\Task;

class Hour extends Report
{
    //what task does it report

    public function task()
    {
        return $this->belongsTo(Task::class)->withDefault([
            'description' => 'Other'
        ]);
    }

    public function billClient()
    {
        return $this->rate_type == 0 ? $this->billable_hours * $this->rate : 0;
    }

    public function earned()
    {
        return $this->billable_hours * $this->rate * $this->share;
    }


    public static function stat($consultant = null, $start = null, $end = null, $review_state = null)
    {
        $bh = 0;
        $nbh = 0;
        $income = 0;
        foreach (self::reported($start, $end, null, $consultant, $review_state, null) as $hour) {
            $bh += $hour->billable_hours;
            $nbh += $hour->non_billable_hours;
            $income += $hour->earned();
        }
        return ['total_bh' => $bh, 'total_nbh' => $nbh, 'total_income' => $income];
    }

    public static function dailyHoursAndIncome($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return self::reported($start, $end, null, $consultant, $review_state, null)
            ->mapToGroups(function ($hour) {
                return [Carbon::parse($hour->report_date)->format('M d') =>
                    [$hour->billable_hours, $hour->non_billable_hours, $hour->earned(), $hour->arrangement_id]];
            })->transform(function ($day) {
                return [$day->sum(0), $day->sum(1), $day->sum(2), $day->pluck(3)->unique()];
            });
    }

    public static function monthlyHoursAndIncome($consultant = null, $start = null, $end = null, $review_state = null, $client = null,$eid=null)
    {
        return self::reported($start, $end, $eid, $consultant, $review_state, $client)
            ->mapToGroups(function ($hour) {
                return [Carbon::parse($hour->report_date)->format('y-M') =>
                    [$hour->billable_hours + $hour->non_billable_hours, $hour->earned()]];
            })->transform(function ($month) {
                return [$month->sum(0), $month->sum(1)];
            });
    }
}
