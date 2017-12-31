<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    public function index($report, Request $request)
    {
        switch ($report) {
            case 'hour':
                return $this->hourApproval($request);
            case 'expense':
                return $this->expenseApproval($request);
        }
        abort(404);
    }


    private function hourApproval($request)
    {
        $confirm = Hour::confirmation($request, Auth::user()->consultant);
        if ($request->get('summary')) {
            return view('selection.approval-select', ['confirm' => $confirm,'report'=>'time']);
        }
        return app(HoursController::class)->index($request, false, $confirm);
    }

    private function expenseApproval($request)
    {
        $confirm = Expense::confirmation($request, Auth::user()->consultant);
        if ($request->get('summary')) {
            return view('selection.approval-select', ['confirm' => $confirm,'report'=>'expense']);
        }
        return app(ExpenseController::class)->index($request, false, $confirm);
    }

}
