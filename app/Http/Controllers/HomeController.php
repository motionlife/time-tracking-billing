<?php

namespace newlifecfo\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     * Dashboard Controller
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ['dates' => $this->getDays(),
            'last_b' => [], 'last_nb' => [], 'last_earn' => [], 'eids' => [], 'total_last_b' => 0, 'total_last_earn' => 0, 'total_last2_earn' => 0, 'last_expense' => 0, 'last2_expense' => 0,
            'total_last_nb' => 0, 'last_buz_dev' => 0, 'last2_buz_dev' => 0, 'last_closings' => 0, 'last2_closings' => 0];
        $consultant = Auth::user()->consultant;

        $lastSum = Hour::dailyHoursAndIncome($consultant, $data['dates']['startOfLast'], $data['dates']['endOfLast'], 1);
        foreach ($lastSum as $day => $amounts) {
            $data['total_last_nb'] += $amounts[1];
            foreach ($amounts[3] as $aid) {
                if (isset($data['eids'][$aid])) {
                    $data['eids'][$aid]++;
                } else {
                    $data['eids'][$aid] = 1;
                }
            }
            if (isset($data['last_b'][$day])) {
                $data['last_b'][$day] += $amounts[0];
                $data['last_earn'][$day] += $amounts[2];
            } else {
                $data['last_b'][$day] = $amounts[0];
                $data['last_earn'][$day] = $amounts[2];
            }
        }
        $last2Total = Hour::stat($consultant, $data['dates']['startOfLast2'], $data['dates']['endOfLast2'], 1);
        $data['total_last2_earn'] = $last2Total['total_income'];
        $data['last_expense'] += Expense::reportedExpenses($consultant, $data['dates']['startOfLast'], $data['dates']['endOfLast'], 1);
        $data['last2_expense'] += Expense::reportedExpenses($consultant, $data['dates']['startOfLast2'], $data['dates']['endOfLast2'], 1);

        $startMon = Carbon::now()->startOfMonth()->subMonth(12)->startOfDay();
        $endMon = Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->endOfDay();
        foreach (Hour::monthlyHoursAndIncome($consultant, $startMon, $endMon, 1)
                 as $mon => $amounts) {
            $data['dates']['mon'][$mon][0] += $amounts[0];
            $data['dates']['mon'][$mon][1] += $amounts[1];
        }
        foreach (Expense::monthlyExpenses($consultant, $startMon, $endMon, 1)
                 as $mon => $amount) {
            $data['dates']['mon'][$mon][1] += $amount;
        }

        foreach ($consultant->dev_clients()->withTrashed()->get() as $dev_client) {
            $engagements = $dev_client->engagements()->withTrashed()->get();
            foreach ($engagements as $engagement) {
                if ($engagement->buz_dev_share == 0) continue;
                $data['last_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast'], $data['dates']['endOfLast'], 1);
                $data['last2_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast2'], $data['dates']['endOfLast2'], 1);
                //todo:: should also calculate last 12 months write buz_dev=>incomeForBuzDev(12 month)
            }
//            foreach (Hour::monthlyHoursAndIncome(null, $startMon, $endMon, 1, $dev_client, $engagements->pluck('id')->toArray())
//                     as $mon => $amounts) {
//                $data['dates']['mon'][$mon][1] += $amounts[1] * $engagement->buz_dev_share;
//            }
        }
        foreach ($consultant->close_engagements()->withTrashed()->get() as $engagement) {
            if ($engagement->closer_share == 0) continue;
            $data['last_closings'] += $engagement->incomeForCloser($data['dates']['startOfLast'], $data['dates']['endOfLast'], 1);
            $data['last2_closings'] += $engagement->incomeForCloser($data['dates']['startOfLast2'], $data['dates']['endOfLast2'], 1);
        }

        $data['total_last_b'] = array_sum($data['last_b']);
        $data['total_last_earn'] = array_sum($data['last_earn']);
        ksort($data['last_earn']);
        ksort($data['last_b']);//data used for plotting the chart

        //data for latest hour report
        $data['recent_hours'] = Hour::reported(null, null, null, $consultant, null)->take(5);
//            return json_encode($data);
        return view('home', ['data' => $data]);
    }

    public function getDays()
    {
        $dates = ['mon' => []];
        if (Carbon::now()->day > 15) {
            $dates['startOfLast'] = Carbon::parse('first day of this month')->startOfDay();
            $dates['endOfLast'] = Carbon::parse('first day of this month')->addDays(14)->endOfDay();
            $dates['startOfLast2'] = Carbon::parse('first day of last month')->addDays(15)->startOfDay();
            $dates['endOfLast2'] = Carbon::parse('last day of last month')->endOfDay();
        } else {
            $dates['startOfLast'] = Carbon::parse('first day of last month')->addDays(15)->startOfDay();
            $dates['endOfLast'] = Carbon::parse('last day of last month')->endOfDay();
            $dates['startOfLast2'] = Carbon::parse('first day of last month')->startOfDay();
            $dates['endOfLast2'] = Carbon::parse('first day of last month')->addDays(14)->endOfDay();
        }
        for ($i = 12; $i > 0; $i--) {
            $dates['mon'][Carbon::now()->startOfMonth()->subMonth($i)->format('y-M')] = [0, 0];
        }
        return $dates;
    }

}
