<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    //the arrangement it belong to
    public function arrangement()
    {
        return $this->belongsTo(Arrangement::class);
    }

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

    public static function recentReports($start = null, $end = null, $eid = null, $consultant = null)
    {
        $arrangements = isset($consultant) ? $consultant->arrangements() : Arrangement::all();
        //todo: consider inconsistent problem caused by deleted arrangement (use soft-delete or status)
        $aids = isset($eid) ? $arrangements->where('engagement_id', $eid)->pluck('id') : $arrangements->pluck('id');

        if ($start || $end)
            return self::whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19'])
                ->whereIn('arrangement_id', $aids)->orderByRaw('report_date DESC, created_at DESC')->get();
        else
            return self::whereIn('arrangement_id', $aids)->orderByRaw('report_date DESC, created_at DESC')->get();
    }

    public function isPending()
    {
        return $this->getStatus()[0] == 'Pending';
    }
    public function unfinalized()
    {
        return ($this->getStatus()[0] == 'Pending' || $this->getStatus()[0] == 'Modified');
    }


    public function getStatus()
    {
        $status = [];
        switch ($this->review_state) {
            case 0:
                $status = ['Pending', 'warning'];
                break;
            case 1:
                $status = ['Approved', 'success'];
                break;
            case 2:
                $status = ['Modified', 'default'];
                break;
            case 3:
                $status = ['BossReplied', 'info'];
                break;
            case 4:
                $status = ['Rejected', 'danger'];
                break;
            case 5:
                $status = ['Archived', 'primary'];
                break;
        }
        return $status;
    }
}
