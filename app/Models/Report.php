<?php

namespace newlifecfo\Models;

use Carbon\Carbon;
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

    //0-just created pending,1 approved, 2 rejected, 3 self confirmed only, 4 leader confirmed only,
    public function isPending()
    {
        return $this->review_state == 0 || $this->review_state == 3;
    }

    public function selfConfirmed()
    {
        return $this->review_state == 3;
    }

    public function leaderConfirmed()
    {
        return $this->review_state == 4;
    }

    public function isApproved()
    {
        return $this->review_state ==1;
    }

    public function unfinalized()
    {
        return $this->review_state == 0 || $this->review_state == 3;
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
                $status = ['Rejected', 'danger'];
                break;
            case 3:
                $status = ['Self_Confirmed', 'primary'];
                break;
            case 4:
                $status = ['Leader_Confirmed', 'info'];
                break;
        }
        return $status;
    }

    public static function reported($start = null, $end = null, $eids = null, $consultant = null, $review_state = null, $client = null)
    {
        $reports = isset($consultant) ? $consultant->reports(get_called_class()) : (isset($client) ? $client->reports(get_called_class()) : self::query());
        $eids = array_values(array_filter(is_array($eids) ? $eids : [], 'is_numeric'));
        if (!empty($eids)) {
            $reports = $reports->whereIn('arrangement_id', Engagement::getAids($eids));
        }
        if ($start || $end) {
            $reports = $reports->whereBetween('report_date', [$start ?: '1970-01-01', $end ?: '2038-01-19']);
        }
        $review_state = $review_state == 7 ? [0, 3, 4] : $review_state;
        if (is_numeric($review_state)) {
            $reports = $reports->where('review_state', $review_state);
        } else if (is_array($review_state)) {
            $states = array_filter($review_state, 'is_numeric');
            if (!empty($states)) $reports = $reports->whereIn('review_state', $states);
        }
        return $reports->orderByRaw('report_date DESC, created_at DESC')->get();
    }

    public static function confirmation($request, $consultant)
    {
        $confirm = [];
        if (Carbon::now()->day > 15) {
            $confirm['startOfLast'] = Carbon::parse('first day of this month')->startOfDay();
            $confirm['endOfLast'] = Carbon::parse('first day of this month')->addDays(14)->endOfDay();
        } else {
            $confirm['startOfLast'] = Carbon::parse('first day of last month')->addDays(15)->startOfDay();
            $confirm['endOfLast'] = Carbon::parse('last day of last month')->endOfDay();
        }
        $eid = explode(',', $request->get('eid'));

        $myReports = self::reported($confirm['startOfLast'], $confirm['endOfLast'], $eid, $consultant, [0, 4]);
        $confirm['count']['me'] = $myReports->count();

        $teamReports = collect();
        $conid = $request->get('conid');
        $consul = isset($conid) ? Consultant::find($conid) : null;
        if ($consul) {
            $eids = $eid[0] ? $eid : $consultant->lead_engagements->pluck('id')->toArray();
            $teamReports = self::reported($confirm['startOfLast'], $confirm['endOfLast'], $eids, $consul, [0, 3], null);
        } else {
            foreach ($consultant->lead_engagements as $engagement) {
                if (!$eid[0] || in_array($engagement->id, $eid))
                    foreach ($engagement->arrangements as $arrangement) {
                        $teamReports = $teamReports->merge(self::reported($confirm['startOfLast'], $confirm['endOfLast'], [$engagement->id], $arrangement->consultant, [0, 3]));
                    }
            }
        }
        $confirm['count']['team'] = $teamReports->count();
        $confirm['reporter'] = $request->get('reporter');
        if ($confirm['reporter'] == 'me') {
            $confirm['reports'] = $myReports;
        } else if ($confirm['reporter'] == 'team') {
            $confirm['reports'] = $teamReports;
        } else {
            $confirm['reports'] = collect();
        }
        return $confirm;
    }

    public static function confirmReport($confirm)
    {
        $feedback = ['code' => 7, 'message' => 'Partially Failed'];
        $reporter = $confirm['reporter'];
        $confirm['reports']->each(function ($report) use ($feedback, $reporter) {
            if ($reporter == 'me') {
                if (!$report->update(['review_state' => $report->leaderConfirmed() ? 1 : 3])) {
                    $feedback['code'] = 0;
                }
            } else if ($reporter == 'team') {
                if (!$report->update(['review_state' => $report->selfConfirmed() ? 1 : 4])) {
                    $feedback['code'] = 0;
                }
            }
        });
        return $feedback;
    }
}
