<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    //the arrangement it belong to
    public function arrangement()
    {
        return $this->belongsTo(Arrangement::class);
    }

    //get the attached receipt
    public function receipt()
    {
        return $this->hasOne(Receipt::class)->withDefault([
            'filename' => 'null',
            'description' => 'null'
        ]);
    }

    public function total()
    {
        return $this->company_paid ? 0 : $this->hotel + $this->flight + $this->meal + $this->office_supply
            + $this->car_rental + $this->mileage_cost + $this->other;
    }

    public function summary(array &$data)
    {
        $day = new Carbon($this->report_date);
        //$key = $day->toDateString();//used if need return the expense of each single date
        if ($day->between($data['dates']['startOfLast'], $data['dates']['endOfLast'])) {
            $data['expense'] += $this->total();
        }
    }
}
