<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    public function index(Request $request, $isAdmin = false)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $eid = $request->get('eid');
        //todo: let user select multiple engagements
        $user = Auth::user();
        $consultant = $isAdmin ? ($request->get('conid') ? Consultant::find($request->get('conid')) : null) : $user->consultant;
        if ($consultant) {
            $hourReports = Hour::recentReports($start, $end, $eid, $consultant, $request->get('state'));
            $expenseReports = Expense::recentReports($start, $end, $eid, $consultant, $request->get('state'));
            $hours = $this->paginate($hourReports, $request->get('perpage') ?: 20, $request->get('tab') == 2 ?: $request->get('page'));
            $expenses = $this->paginate($expenseReports, $request->get('perpage') ?: 20, $request->get('tab') != 2 ?: $request->get('page'));
            return view('wage', ['clientIds' => Engagement::groupedByClient($consultant),
                'hours' => $hours, 'expenses' => $expenses,
                'income' => $this->getIncome($consultant, $start, $end, $eid),
                'buz_devs' => $this->getBuzDev($consultant, $start, $end, $eid),
                'admin' => $isAdmin,
                'consultant' => $consultant,
            ]);
        } else {
            $incomes=[];
            $buz_dev_incomes=[];
            $consultants = Consultant::all();
            foreach ($consultants as $consultant) {
                $incomes[$consultant->id]=$this->getIncome($consultant,$start,$end,$eid);
                $buz_dev_incomes[$consultant->id]=$this->getBuzDev($consultant, $start, $end, $eid)['total'];
            }
            return view('wage',['clientIds' => Engagement::groupedByClient(null),
                'admin'=>$isAdmin,
                'consultants'=>$consultants,
                'incomes'=>$incomes,
                'buzIncomes'=>$buz_dev_incomes
            ]);
        }
    }

    //todo : dealing with status query request
    private function getIncome(Consultant $consultant, $start, $end, $eid)
    {
        $total_bh = 0;
        $total_ex = 0;
        foreach ($consultant->arrangements as $arr) {
            if (!isset($eid) || $arr->engagement_id == $eid) {
                $total_bh += $arr->hoursIncomeForConsultant($start, $end);
                $total_ex += $arr->reportedExpenses($start, $end);
            }
        }
        return [$total_bh, $total_ex];
    }

    private function getBuzDev(Consultant $consultant, $start, $end, $eid)
    {
        $total = 0;
        $engs = [];
        foreach ($consultant->dev_clients as $dev_client) {
            foreach ($dev_client->engagements as $engagement) {
                if (!isset($eid) || $engagement->id == $eid) {
                    $devs = $engagement->incomeForBuzDev($start, $end);
                    if ($devs) {
                        array_push($engs, [$engagement, $devs]);
                        $total += $devs;
                    }
                }
            }
        }
        return ['total' => $total, 'engs' => $engs];
    }
}
