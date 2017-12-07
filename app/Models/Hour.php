<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use newlifecfo\Models\Templates\Task;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Hour extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
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

    //deprecated
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

    public static function recentReports($start = null, $end = null, $eid = null, $consultant = null)
    {
        $arrangements = isset($consultant) ? $consultant->arrangements() : Arrangement::all();
        //todo: consider inconsistent problem caused by deleted arrangement (use soft-delete or status)
        //$arrangements = isset($consultant) ? $consultant->arrangements()->withTrashed() : Arrangement::withTrashed();
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
