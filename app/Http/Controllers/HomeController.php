<?php

namespace newlifecfo\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index(Request $request)
    {
        $data = ['dates' => $this->getDays(),
            'last_b' => [], 'last_nb' => [], 'last_earn' => [], 'eids' => [], 'total_last_b' => 0, 'total_last_earn' => 0, 'total_last2_earn' => 0, 'last_expense' => 0, 'last2_expense' => 0,
            'total_last_nb' => 0, 'last_buz_dev' => 0, 'last2_buz_dev' => 0,];
        $consultant = Auth::user()->consultant;
        foreach ($consultant->arrangements as $arr) {
            //todo: to deal with the case where it's engagement hab been deleted, Alter Arrangement table add status column
            if (!$arr->engagement) continue;
            foreach ($arr->dailyHoursAndIncome($data['dates']['startOfLast'], $data['dates']['endOfLast'], $arr->engagement->id) as $day => $amounts) {
                $data['total_last_nb'] += $amounts[1];
                if (isset($data['eids'][$amounts[3]])) $data['eids'][$amounts[3]]++; else $data['eids'][$amounts[3]] = 1;
                if (isset($data['last_b'][$day])) {
                    $data['last_b'][$day] += $amounts[0];
                    $data['last_earn'][$day] += $amounts[2];
                } else {
                    $data['last_b'][$day] = $amounts[0];
                    $data['last_earn'][$day] = $amounts[2];
                }
            }
            $data['total_last2_earn'] += $arr->hoursIncomeForConsultant($data['dates']['startOfLast2'], $data['dates']['endOfLast2']);
            $data['last_expense'] += $arr->reportedExpenses($data['dates']['startOfLast'], $data['dates']['endOfLast']);
            $data['last2_expense'] += $arr->reportedExpenses($data['dates']['startOfLast2'], $data['dates']['endOfLast2']);

            foreach ($arr->monthlyHoursAndIncome(Carbon::now()->subMonth(12)->startOfMonth()->startOfDay(), Carbon::now()->subMonth()->endOfMonth()->endOfDay())
                     as $mon => $amounts) {
                $data['dates']['mon'][$mon][0] += $amounts[0];
                $data['dates']['mon'][$mon][1] += $amounts[1];
            };

            foreach ($arr->monthlyExpenses(Carbon::now()->subMonth(12)->startOfMonth()->startOfDay(), Carbon::now()->subMonth()->endOfMonth()->endOfDay())
                     as $mon => $amount) {
                $data['dates']['mon'][$mon][1] += $amount;
            }
        }

        foreach ($consultant->dev_clients as $dev_client) {
            foreach ($dev_client->engagements as $engagement) {
                if ($engagement->buz_dev_share == 0) continue;
                $data['last_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast'], $data['dates']['endOfLast']);
                $data['last2_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast2'], $data['dates']['endOfLast2']);
                foreach ($engagement->arrangements as $arr) {
                    foreach ($arr->monthlyHoursAndIncome(Carbon::now()->subMonth(12)->startOfMonth()->startOfDay(), Carbon::now()->subMonth()->endOfMonth()->endOfDay())
                             as $mon => $amounts) {
                        $data['dates']['mon'][$mon][1] += $amounts[1] * $engagement->buz_dev_share;
                    };
                }
            }
        }

        $data['total_last_b'] = array_sum($data['last_b']);
        $data['total_last_earn'] = array_sum($data['last_earn']);
        ksort($data['last_earn']);
        ksort($data['last_b']);//data used for plotting the chart

        //data for latest hour report
        $data['recent_hours'] = Hour::recentReports(null,null,null,$consultant)->take(5);
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
