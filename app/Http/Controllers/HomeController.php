<?php

namespace newlifecfo\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (Auth::user()->hasIncome()) {
            $data = ['dates' => $this->getDays(),
                'last_b' => [], 'last_nb' => [], 'last_earn' => [], 'eids' => [],
                'total_last2_earn' => 0, 'last_expense' => 0, 'last2_expense' => 0,
                'last_buz_dev' => 0, 'last2_buz_dev' => 0,];
            $consultant = Auth::user()->entity;
            foreach ($consultant->arrangements as $arr) {
                $data['net_rate'] = $arr->billing_rate * (1 - $arr->firm_share);
                foreach ($arr->hours as $hour) {
                    $hour->summary($data);
                }
                foreach ($arr->expenses as $expense) {
                    $expense->summary($data);
                }
                foreach ($data['dates']['months'] as $i => $month) {
                    $data['dates']['m-income'][$i] += $arr->hoursIncomeForConsultant($month, $month->copy()->endOfMonth()->endOfDay())
                        + $arr->reportedExpenses($month, $month->copy()->endOfMonth()->endOfDay());
                    $data['dates']['m-hours'][$i] += $arr->reportedHours($month, $month->copy()->endOfMonth()->endOfDay());
                }

            }
            foreach ($consultant->dev_clients as $dev_client) {
                foreach ($dev_client->engagements as $engagement) {
                    $data['last_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast'], $data['dates']['endOfLast']);
                    $data['last2_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast2'], $data['dates']['endOfLast2']);
                    foreach ($data['dates']['months'] as $i => $month) {
                        $data['dates']['m-income'][$i] += $engagement->incomeForBuzDev($month, $month->copy()->endOfMonth()->endOfDay());
                    }
                }
            }

            $this->monthly_sum($data);
            ksort($data['last_earn']);
            ksort($data['last_b']);//data used for plotting the chart
//            return json_encode($data);
            return view('home', ['data' => $data]);
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }

    private function monthly_sum(array &$data)
    {
        $data['total_last_b'] = 0;
        $data['total_last_nb'] = 0;
        $data['total_last_earn'] = 0;
        foreach ($data['last_b'] as $b) $data['total_last_b'] += $b;
        foreach ($data['last_nb'] as $nb) $data['total_last_nb'] += $nb;
        foreach ($data['last_earn'] as $earn) $data['total_last_earn'] += $earn;
    }

    public function getDays()
    {
        $dates = [];
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
            $dates['months'][] = Carbon::now()->subMonth($i)->startOfMonth()->startOfDay();
            $dates['m-income'][] = $dates['m-hours'][] = 0;
        }
        return $dates;
    }

}
