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
        return $this->belongsTo(Arrangement::class)->withDefault();
    }

    public function consultant()
    {
        return $this->belongsTo(Consultant::class)->withDefault(function ($consultant) {
            $consultant->first_name = 'Deleted';
            $consultant->last_name = 'Deleted';
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withDefault(function ($client) {
            $client->name = "Deleted";
        });
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

    public static function reported($start = null, $end = null, $eid = null, $consultant = null, $review_state = null, $client = null)
    {
        $reports = isset($consultant) ? $consultant->reports(get_called_class()) : (isset($client) ? $client->reports(get_called_class()) : self::query());
        if ($eid[0]) {
            $reports = $reports->whereIn('arrangement_id', Engagement::getAids($eid));
        }
        if ($start || $end) {
            $reports = $reports->whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19']);
        }
        $reports = $reports->orderByRaw('report_date DESC, created_at DESC');
        return isset($review_state) ? $reports->where('review_state', $review_state)->get() : $reports->get();
    }
}
