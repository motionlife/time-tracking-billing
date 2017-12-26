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
            'description'=>'Other'
        ]);
    }

    /**
     * @deprecated
     */
    public function summary(array &$data)
    {
        $day = new Carbon($this->report_date);
        $key = $day->toDateString();
        if ($day->between($data['dates']['startOfLast'], $data['dates']['endOfLast'])) {
            $earned = $this->billable_hours * $data['net_rate'];
            if (isset($data['last_b'][$key])) {
                $data['last_b'][$key] += $this->billable_hours;
                $data['last_nb'][$key] += $this->non_billable_hours;
                $data['last_earn'][$key] += $earned;
            } else {
                $data['last_b'][$key] = $this->billable_hours;
                $data['last_nb'][$key] = $this->non_billable_hours;
                $data['last_earn'][$key] = $earned;
            }
            $eid = $this->arrangement->engagement->id;
            $data['eids'][$eid] = isset($data['eids'][$eid]) ? $data['eids'][$eid] + $this->billable_hours : $this->billable_hours;
        } else if ($day->between($data['dates']['startOfLast2'], $data['dates']['endOfLast2'])) {
            $data['total_last2_earn'] += $this->billable_hours * $data['net_rate'];
        }
    }

    public function earned()
    {
        return $this->billable_hours * $this->rate * $this->share;
    }

    public static function filter($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return (isset($consultant) ? $consultant->hours()->whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19']) :
            self::whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19']))
            ->where('review_state', isset($review_state) ? '=' : '<>', isset($review_state) ? $review_state : 7)->get();
    }

    public static function stat($consultant = null, $start = null, $end = null, $review_state = null)
    {
        $bh = 0;
        $nbh = 0;
        $income = 0;
        foreach (self::filter($consultant, $start, $end, $review_state) as $hour) {
            $bh += $hour->billable_hours;
            $nbh += $hour->non_billable_hours;
            $income += $hour->earned();
        }
        return ['total_bh' => $bh, 'total_nbh' => $nbh, 'total_income' => $income];
    }

    public static function dailyHoursAndIncome($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return self::filter($consultant, $start, $end, $review_state)
            ->mapToGroups(function ($hour) {
                return [Carbon::parse($hour->report_date)->format('M d') =>
                    [$hour->billable_hours, $hour->non_billable_hours, $hour->earned(),$hour->arrangement_id]];
            })->transform(function ($day) {
                return [$day->sum(0), $day->sum(1), $day->sum(2),$day->pluck(3)->unique()];
            });
    }
    public static function monthlyHoursAndIncome($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return self::filter($consultant, $start, $end, $review_state)
            ->mapToGroups(function ($hour) {
                return [Carbon::parse($hour->report_date)->format('y-M') =>
                    [ $hour->billable_hours + $hour->non_billable_hours , $hour->earned()]];
            })->transform(function ($month) {
                return [$month->sum(0), $month->sum(1)];
            });
    }


}
