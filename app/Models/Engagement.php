<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use newlifecfo\User;

class Engagement extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
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

    public static function groupedByClient($consultant = null)
    {
        if (isset($consultant)) $eids = $consultant->arrangements()->pluck('engagement_id');
        return (isset($consultant) ? self::all()->whereIn('id', $eids) : self::all())
            ->mapToGroups(function ($item, $key) {
                return [$item->client_id => [$item->id, $item->name]];
            });
    }

    //get the leader(consultant) of the engagement
    public function leader()
    {
        return $this->belongsTo(Consultant::class, 'leader_id');
    }

    //'0=/hourly,1=/15-day,2=/month,3=/year,4=engagement fixed,..'
    public function clientBilledType()
    {
        switch ($this->paying_cycle) {
            case 0:
                return 'Hourly';
            case 1:
                return 'Monthly';
            case 2:
                return 'Semi-monthly';
            case 3:
                return 'Engagement Fixed';
        }
        return 'Unknown';
    }

    public static function billedType($cycle)
    {
        switch ($cycle) {
            case 0:
                return 'Hourly';
            case 1:
                return 'Monthly';
            case 2:
                return 'Semi-monthly';
            case 3:
                return 'Engagement Fixed';
        }
        return 'Unknown';
    }

    public function isPending()
    {
        return $this->state() == 'Pending';
    }

    public function isActive()
    {
        return $this->state() == 'Active';
    }

    public function isClosed()
    {
        return $this->state() == 'Closed';
    }

    public function state()
    {
        switch ($this->status) {

            case 0:
                return 'Active';//Operating, running
            case 1:
                return 'Pending';//when just created before approval by boss
            case 2:
                return 'Closed';
            case 3:
                return 'non-deletable';
        }
        return 'Unknown';
    }

    public function getStatusLabel()
    {
        return $this->isActive() ? 'success' : ($this->isClosed() ? 'default' : 'warning');
    }

    public function clientLaborBills($start = null, $end = null)
    {
        //For monthly labor billing, detail not implemented yet...
        //todo: dealing with different client-billing type
        if ($this->paying_cycle != 0) {
            $start = Carbon::parse($start ?: $this->start_date);
            $end = $end ? Carbon::parse($end) : Carbon::now();
            $days = $start->diffInDays($end);
            if ($this->paying_cycle == 1) {
                return $this->cycle_billing / 30 * $days;
            } else if ($this->paying_cycle == 2) {
                return $this->cycle_billing / 15 * $days;
            } else if ($this->paying_cycle == 3 && $this->isClosed()) {
                return $this->cycle_billing;
            } else {
                return 0;
            }
        } else {
            $total = 0;
            foreach ($this->arrangements as $arr) {
                $total += $arr->hoursBillToClient($start ?: '1970-01-01', $end ?: '2038-01-19');
            }
            return $total;
        }
    }

    public function clientExpenseBills($start = '1970-01-01', $end = null)
    {
        $total = 0;
        foreach ($this->arrangements as $arr) {
            $total += $arr->reportedExpenses($start, $end);
        }
        return $total;
    }

    public function incomeForBuzDev($start = null, $end = null)
    {
        return $this->buz_dev_share ? $this->clientLaborBills($start ?: '1970-01-01', $end ?: '2038-01-19') * $this->buz_dev_share : 0;
    }

    public static function getBySCLS($start = null, $cid = null, $leader = null, $consultant = null, $status = null)
    {
        $collection1 = (isset($leader) ? $leader->lead_engagements : self::all())
            ->where('start_date', '>=', $start ? Carbon::parse($start)->toDateString('Y-m-d') : '1970-01-01')
            ->sortByDesc('created_at');
        $collection2 = (isset($cid) ? $collection1->where('client_id', $cid) : $collection1);
        $collection3 = isset($status) ? $collection2->where('status', $status) : $collection2;
        return isset($consultant) ? $collection3->whereIn('id', $consultant->arrangements()->pluck('engagement_id')) : $collection3;
    }
}
