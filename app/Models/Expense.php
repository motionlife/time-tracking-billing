<?php

namespace newlifecfo\Models;

use Carbon\Carbon;

class Expense extends Report
{
    //get the attached receipt
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function total()
    {
        return $this->company_paid ? 0 : $this->hotel + $this->flight + $this->meal + $this->office_supply
            + $this->car_rental + $this->mileage_cost + $this->other;
    }

    /**
     * @deprecated
     */
    public function summary(array &$data)
    {
        $day = new Carbon($this->report_date);
        //$key = $day->toDateString();//used if need return the expense of each single date
        if ($day->between($data['dates']['startOfLast'], $data['dates']['endOfLast'])) {
            $data['expense'] += $this->total();
        } else if ($day->between($data['dates']['startOfLast2'], $data['dates']['endOfLast2'])) {
            $data['last2_expense'] += $this->total();
        }
    }

    public static function filter($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return (isset($consultant) ? $consultant->expenses()->whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19']) :
            self::whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19']))
            ->where('review_state', isset($review_state) ? '=' : '<>', isset($review_state) ? $review_state : 7)->get();
    }

    public static function monthlyExpenses($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return self::filter($consultant, $start, $end, $review_state)
            ->mapToGroups(function ($exp) {
                return [Carbon::parse($exp->report_date)->format('y-M') => $exp->total()];
            })->transform(function ($month) {
                return $month->sum();
            });
    }

    public static function reportedExpenses($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return self::filter($consultant, $start, $end, $review_state)->sum(function ($exp) {
            return $exp->total();
        });
    }
}
