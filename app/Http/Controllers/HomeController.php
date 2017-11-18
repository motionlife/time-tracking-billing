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
                    $data['last_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast'], $data['dates']['endOfLast']);
                    $data['last2_buz_dev'] += $engagement->incomeForBuzDev($data['dates']['startOfLast2'], $data['dates']['endOfLast2']);
                    //$engagement->monthlyIncomeForBuzDev(Carbon::now()->subMonth(12)->startOfMonth()->startOfDay(), Carbon::now()->subMonth()->endOfMonth()->endOfDay(), $data['dates']['mon']);
                    foreach ($engagement->arrangements as $arr) {
                        foreach ($arr->monthlyHoursAndIncome(Carbon::now()->subMonth(12)->startOfMonth()->startOfDay(), Carbon::now()->subMonth()->endOfMonth()->endOfDay())
                                 as $mon => $amounts) {
                            $data['dates']['mon'][$mon][1] += $amounts[1];
                        };
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
            $dates['mon'][Carbon::now()->subMonth($i)->format('y-M')] = [0, 0];
        }
        return $dates;
    }

}
