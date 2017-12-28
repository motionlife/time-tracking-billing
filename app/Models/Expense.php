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


    public static function monthlyExpenses($consultant = null, $start = null, $end = null, $review_state = null)
    {
        return self::filter($consultant, $start, $end, $review_state)
            ->mapToGroups(function ($exp) {
                return [Carbon::parse($exp->report_date)->format('y-M') => $exp->total()];
            })->transform(function ($month) {
                return $month->sum();
            });
    }

    public static function reportedExpenses($consultant = null, $start = null, $end = null, $review_state = null,$client=null)
    {
        return self::filter($consultant, $start, $end, $review_state,$client)->sum(function ($exp) {
            return $exp->total();
        });
    }
}
