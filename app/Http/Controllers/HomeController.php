<?php

namespace newlifecfo\Http\Controllers;

use Ds\Set;
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
        $eids = [];
        $data = ['weekly' => [['bh' => 0, 'nbh' => 0, 'earn' => 0], ['bh' => 0, 'nbh' => 0, 'earn' => 0], ['bh' => 0, 'nbh' => 0, 'earn' => 0], ['bh' => 0, 'nbh' => 0, 'earn' => 0], ['bh' => 0, 'nbh' => 0, 'earn' => 0], ['bh' => 0, 'nbh' => 0, 'earn' => 0], ['bh' => 0, 'nbh' => 0, 'earn' => 0]],
            'eids' => $eids];
        $consultant = Auth::user()->entity;
        foreach ($consultant->arrangements as $arr) {
            $rate = $arr->billing_rate * (1 - $arr->firm_share);
            foreach ($arr->hours as $hour) {
                $hour->contributed_hours_last_week($data, $rate);
            }
        }

        $this->weekly_sum($data['weekly']);
//        return json_encode($data['eids']);
        return view('home', ['data' => $data]);
    }

    private function weekly_sum(array &$weekly)
    {
        $sum = [0, 0, 0];
        foreach ($weekly as $day) {
            $sum[0] += $day['bh'];
            $sum[1] += $day['nbh'];
            $sum[2] += $day['earn'];
        }
        array_push($weekly, $sum);
    }

}
