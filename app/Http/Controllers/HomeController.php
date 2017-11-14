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
        $consultants = Consultant::all();
        $output = [];
        foreach ($consultants as $consultant) {
            $totalpay = 0;
            $totalbillablehour = 0;
            $totalnonbillablehour = 0;
            foreach ($consultant->arrangements as $arrangement) {
                $billing_rate = $arrangement->billing_rate;
                $firm_share = $arrangement->firm_share;
                $hourlypay = $billing_rate * (1 - $firm_share);
                foreach ($arrangement->hourReports as $hour) {
                    $totalpay += $hour->billable_hours * $hourlypay;
                    $totalbillablehour += $hour->billable_hours;
                    $totalnonbillablehour += $hour->non_billable_hours;
                }
            }
            array_push($output, array('name' => $consultant->fullname(),
                'totalbh' => $totalbillablehour,
                'totalnbh' => $totalnonbillablehour,
                'totalpay' => $totalpay));
        }

        return view('test',['consultants'=>$consultants,'result'=>$output]);
    }
}
