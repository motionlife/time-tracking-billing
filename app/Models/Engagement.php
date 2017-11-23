<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Engagement extends Model
{
    protected $guarded = [];

    //get all the arrangements that attached to this engagement
    public function arrangements()
    {
        return $this->hasMany(Arrangement::class);
    }

    //get the client who initiated the engagement
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    //get the leader(consultant) of the engagement
    public function leader()
    {
        return $this->belongsTo(Consultant::class,'leader_id');
    }

    //'0=/hourly,1=/15-day,2=/month,3=/year,4=engagement fixed,..'
    public function clientBilledType()
    {
        switch ($this->paying_cycle) {

            case 0:
                return 'Hourly';
            case 1:
                return 'Semi-monthly';
            case 2:
                return 'Monthly';
            case 3:
                return 'Engagement Fixed';
        }
        return 'Unknown';
    }
    public function state()
    {
        switch ($this->status) {

            case 0:
                return 'Active';
            case 1:
                return 'Pending';
            case 2:
                return 'Closed';
        }
        return 'Unknown';
    }

    public function clientLaborBills($start = '1970-01-01', $end = '2038-01-19')
    {
        //For monthly labor billing, detail not implemented yet...
        if ($this->paying_cycle != 0) return $this->cycle_billing;
        $total = 0;
        foreach ($this->arrangements as $arr) {
            $total += $arr->hoursBillToClient($start, $end);
        }
        return $total;
    }

    public function clientExpenseBills($start = '1970-01-01', $end = '2038-01-19')
    {
        $total = 0;
        foreach ($this->arrangements as $arr) {
            $total += $arr->reportedExpenses($start, $end);
        }
        return $total;
    }

    public function incomeForBuzDev($start = '1970-01-01', $end = '2038-01-19')
    {
        return $this->buz_dev_share ? $this->clientLaborBills($start, $end) * $this->buz_dev_share : 0;
    }

    public function isClosed()
    {
        return $this->status == 1;
    }
}
