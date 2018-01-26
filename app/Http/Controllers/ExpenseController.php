<?php

namespace newlifecfo\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Receipt;
use newlifecfo\Models\Report;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param bool $isAdmin
     * @param bool $confirm
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $isAdmin = false, $confirm = false)
    {
        $consultant = $isAdmin ? ($request->get('conid') ? Consultant::find($request->get('conid')) : null) : Auth::user()->consultant;
        $expenses = $this->paginate($confirm ? $confirm['reports'] : Expense::reported($request->get('start'),
            $request->get('end'), explode(',', $request->get('eid')), $consultant, $request->get('state')), 25);
        if ($request->ajax() && $confirm && $request->get('submit') == 'confirm') {
            return Report::confirmReport($confirm);
        }
        return view('expenses', ['expenses' => $expenses,
            'clientIds' => Engagement::groupedByClient($consultant),
            'admin' => $isAdmin,
            'confirm' => $confirm
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $consultant = Auth::user()->consultant;
        //same reported hours
        $feedback = [];
        $eid = $request->get('eid');
        if ($request->ajax()) {
            //business logic validation is important
            //1. check the if the reported engagement is his valid engagement
            $eng = Engagement::find($eid);
            if (!$eng || !$eng->isActive()) {
                $feedback['code'] = 1;
                $feedback['message'] = 'Non-active Engagement!, has it been closed or still pending? Please contact supervisor.';
            } else {
                $arr = $consultant->getMyArrangementByEidPid($eid);
                if (!$arr) {
                    $feedback['code'] = 2;
                    $feedback['message'] = 'You are not in this engagement';
                } else {
                    $exp = (new Expense(['arrangement_id' => $arr->id, 'consultant_id' => $arr->consultant_id, 'client_id' => $eng->client_id]))->fill($request->except(['eid', 'receipts', 'review_state']));
                    if ($exp->save()) {
                        if ($this->saveReceipts($request, $exp->id)) {
                            $feedback['code'] = 7;
                            $feedback['message'] = 'success';
                            $feedback['data'] = ['company_paid' => $exp->company_paid ? 'Yes' : 'No', 'status' => $exp->getStatus(), 'total' => $exp->total(),
                                'report_date' => $exp->report_date, 'description' => str_limit($exp->description, 22), 'receipts' => $exp->receipts->pluck('filename'),
                                'ename' => str_limit($eng->name, 22), 'cname' => str_limit($eng->client->name, 37), 'expid' => $exp->id];
                        } else {
                            $exp->delete();//rollback newly saved receipt record if  saving file failed
                            $feedback['code'] = 4;
                            $feedback['message'] = 'Receipts file saving failed, try to upload one by one.';
                        }

                    } else {
                        $feedback['code'] = 3;
                        $feedback['message'] = 'unknown error happened while saving';
                    }
                }
            }
            return json_encode($feedback);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $user = Auth::user();
        if ($request->ajax()) {
            $expense = Expense::find($id);
            if ($user->can('view', $expense)) {
                $arr = $expense->arrangement;
                $expense->report_date = Carbon::parse($expense->report_date)->format('m/d/Y');
                return json_encode(['receipts' => $expense->receipts, 'ename' => $arr->engagement->name,'client'=>$expense->client->name, 'report_date' => $expense->report_date, 'company_paid'=>$expense->company_paid,'description' => $expense->description,
                    'review_state' => $expense->review_state, 'hotel' => $expense->hotel, 'flight' => $expense->flight, 'meal' => $expense->meal,
                    'office_supply' => $expense->office_supply, 'car_rental' => $expense->car_rental, 'mileage_cost' => $expense->mileage_cost, 'other' => $expense->other, 'total' => number_format($expense->total(), 2, '.', ''),
                    'cname' => $arr->consultant->fullname(), 'feedback' => $expense->feedback
                ]);
            }
            //else illegal request!
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        //same reported hours
        $feedback = [];
        if ($request->ajax()) {
            $expense = Expense::find($id);
            if ($user->can('update', $expense)) {
                $excepted = $user->isSupervisor() && $request->get('review_state') != 'undefined' ? ['eid', 'receipts'] : ['eid', 'receipts', 'review_state'];
                if ($expense->update($request->except($excepted))) {
                    if ($this->saveReceipts($request, $expense->id)) {
                        $feedback['code'] = 7;
                        $feedback['message'] = 'Record Update Success';
                        $feedback['record'] = ['company_paid' => $expense->company_paid ? 'Yes' : 'No', 'total' => number_format($expense->total(), 2),
                            'report_date' => $expense->report_date, 'description' => $expense->description, 'receipts' => $expense->receipts->pluck('filename'),
                            'status' => $expense->getStatus(),];
                    } else {
                        $feedback['code'] = 6;
                        $feedback['message'] = 'Adding Files Failed, expense update rollback';
                    }
                } else {
                    $feedback['code'] = 4;
                    $feedback['message'] = 'unknown error during updating';
                }

            } else {
                $feedback['code'] = 1;
                $feedback['message'] = 'Cannot be updated now, no authorization';
            }
            return json_encode($feedback);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //
        $user = Auth::user();
        if ($request->ajax()) {
            $expense = Expense::find($id);
            //must check if this $expense record belong to the consultant!!!
            if ($user->can('delete', $expense)) {
                if ($expense->delete()) return json_encode(['message' => 'succeed']);
            }
            return json_encode(['message' => 'delete_failed']);
        }
    }

    /**
     * Save the receipt files if uploaded with expense data
     */
    private function saveReceipts(Request $request, $expid)
    {
        //user didn't upload any receipt file allow them just save the expense data
        if (!$request->hasFile('receipts')) {
            return true;
        }
        $saved = false;
        foreach ($request->receipts as $receipt) {
            $filename = $receipt->store('receipts');
            $saved = (new Receipt(['expense_id' => $expid, 'filename' => $filename]))->save();
        }
        return $saved;
    }
}
