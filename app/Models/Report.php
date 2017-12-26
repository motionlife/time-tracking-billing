<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
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

    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public static function recentReports($start = null, $end = null, $eid = null, $consultant = null, $status = null)
    {
        $arrangements = isset($consultant) ? $consultant->arrangements() : Arrangement::all();
        //todo: consider inconsistent problem caused by deleted arrangement (use soft-delete or status)
        //$arrangements = isset($consultant) ? $consultant->arrangements()->withTrashed() : Arrangement::withTrashed();
        $aids = $eid[0] ? $arrangements->whereIn('engagement_id', $eid)->pluck('id') : $arrangements->pluck('id');
        if ($start || $end)
            $qbuilder = self::whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19'])
                ->whereIn('arrangement_id', $aids)->orderByRaw('report_date DESC, created_at DESC');
        else
            $qbuilder = self::whereIn('arrangement_id', $aids)->orderByRaw('report_date DESC, created_at DESC');
        return isset($status) ? $qbuilder->where('review_state', $status)->get() : $qbuilder->get();
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
                $status = ['Confirmed', 'default'];
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
