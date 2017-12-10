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

    public function index(Request $request)
    {
        //todo: let user select multiple engagements
        $user = Auth::user();
        $consultant = $request->get('conid')?Consultant::find($request->get('conid')):$user->consultant;
        $hourReports = Hour::recentReports($request->get('start'), $request->get('end'), $request->get('eid'), $consultant);
        $expenseReports = Expense::recentReports($request->get('start'), $request->get('end'), $request->get('eid'), $consultant);

        $hourIncome = $this->getHourIncome($hourReports);
        $expenseIncome = $this->getExpenseIncome($expenseReports);

        $hours = $this->paginate($hourReports, 25);
        $expenses = $this->paginate($expenseReports, 25);

        return view('wage', ['clientIds' => Engagement::groupedByClient($consultant),
            'hours' => $hours, 'expenses' => $expenses,
            'hourIncome' => $hourIncome, 'expenseIncome' => $expenseIncome,
        ]);
    }

    private function getHourIncome($hourReports)
    {
        $total = 0;
        foreach ($hourReports as $report) {
            $billable_hours = $report->billable_hours;
            if ($billable_hours) {
                $arr = $report->arrangement;
                $total += $billable_hours * $arr->billing_rate * (1 - $arr->firm_share);
            }
        }
        return $total;
    }

    private function getExpenseIncome($expenseReports)
    {
        $total = 0;
        foreach ($expenseReports as $report) {
            $total += $report->total();
        }
        return $total;
    }
}
