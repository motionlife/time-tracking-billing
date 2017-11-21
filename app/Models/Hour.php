<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Task;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function couldBeDeleted()
    {
        return $this->getStatus()[0]=='Pending';
    }

    public function couldBeUpdated()
    {
        $status = $this->getStatus();
        return ($status[0]=='Pending'||$status[0]=='Modified');
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
                $status = ['Modified', 'info'];
                break;
            case 3:
                $status = ['BossReplied', 'default'];
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
