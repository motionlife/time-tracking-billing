<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Consultant;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
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
    public function index()
    {
        $consultant = Consultant::where('first_name', 'Diego')->first();
        $totalpay = 0;
        $totalbillablehour = 0;
        $totalnonbillablehour = 0;
        foreach ($consultant->arrangements as $arrangement) {
            $billing_rate = $arrangement->billing_rate;
            $firm_share = $arrangement->firm_share;
            $hourlypay = $billing_rate * (1 - $firm_share);
            echo '<ul>' . $arrangement->engagement->name;
            echo '<li>' . $arrangement->billing_rate . '</li>';
            foreach ($arrangement->hourReports as $hour) {
                $totalpay += $hour->billable_hours * $hourlypay;
                $totalbillablehour += $hour->billable_hours;
                $totalnonbillablehour += $hour->non_billable_hours;
                echo '<li>billable_hour:' . $hour->billable_hours . ';nonbillable_hour:' . $hour->non_billable_hours;
            }
            echo '</ul>';
        }
        return json_encode(['name' => $consultant->fullname(),
            'total_bh' => $totalbillablehour,
            'total_non_bh' => $totalnonbillablehour,
            'total' => $totalpay]);
        //return view('home');
    }
}
