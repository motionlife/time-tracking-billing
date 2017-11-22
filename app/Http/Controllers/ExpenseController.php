<?php

namespace newlifecfo\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Receipt;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $consultant = Auth::user()->entity;
        $expenses = $this->paginate($consultant->recentHourOrExpenseReports($request->get('start'),
            $request->get('end'), $request->get('eid'), false), 25);
        return view('expenses', ['expenses' => $expenses,
            'clientIds' => $consultant->EngagementByClient()]);
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
        $consultant = Auth::user()->entity;
        //same reported hours
        $feedback = [];
        $eid = $request->get('eid');
        $filename = $request->get('receipts');
        if ($request->ajax()) {
            //business logic validation is important
            //1. check the if the reported engagement is his valid engagement
            $eng = Engagement::find($eid);
            if (!$eng) {
                $feedback['code'] = 0;
                $feedback['message'] = 'Engagement not found.';
            } else if ($eng->isClosed()) {
                $feedback['code'] = 1;
                $feedback['message'] = 'The Engagement has been closed or not valid any more.';
            } else {
                $arr = $consultant->getArrangementByEidPid($eid);
                if (!$arr) {
                    $feedback['code'] = 2;
                    $feedback['message'] = 'You are not in this engagement';
                } else {
                    $exp = (new Expense(['arrangement_id' => $arr->id]))->fill($request->except(['eid', 'receipts']));
                    if ($exp->save()) {
                        if (!$filename || (new Receipt(['expense_id' => $exp->id, 'filename' => $filename]))->save()) {
                            $feedback['code'] = 7;
                            $feedback['message'] = 'success';
                            $feedback['data'] = ['company_paid' => $exp->company_paid ? 'Yes' : 'No', 'status' => $exp->getStatus(), 'total' => $exp->total(),
                                'report_date' => $exp->report_date, 'description' => str_limit($exp->description, 22), 'receipts' => $exp->receipts,
                                'ename' => str_limit($eng->name, 22), 'cname' => str_limit($eng->client->name, 37), 'expid' => $exp->id];
                        } else {
                            $exp->delete();//rollback newly saved receipt record due to saving file failed
                            $feedback['code'] = 4;
                            $feedback['message'] = 'Receipts file saving failed';
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
        //
        $consultant = Auth::user()->entity;
        if ($request->ajax()) {
            $expense = Expense::find($id);
            //must check if this hour record belong to the consultant!!!
            if ($expense && $expense->arrangement->consultant_id == $consultant->id) {
                $arr = $expense->arrangement;
                $expense->report_date = Carbon::parse($expense->report_date)->format('m/d/Y');
                return json_encode(['receipts' => $expense->receipts, 'ename' => $arr->engagement->name, 'report_date' => $expense->report_date, 'description' => $expense->description,
                    'review_state' => $expense->review_state, 'hotel' => $expense->hotel, 'flight' => $expense->flight, 'meal' => $expense->meal,
                    'office_supply' => $expense->office_supply, 'car_rental' => $expense->car_rental, 'mileage_cost' => $expense->mileage_cost, 'other' => $expense->other,'total'=>number_format($expense->total(),2,'.',''),
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
        $consultant = Auth::user()->entity;
        //same reported hours
        $feedback = [];
        if ($request->ajax()) {
            //business logic validation is important
            //1. check the if the reported engagement is his valid engagement
            $expense = Expense::find($id);
            if (!$expense || $expense->arrangement->consultant_id != $consultant->id) {
                $feedback['code'] = 0;
                $feedback['message'] = 'Record not found or no authorization';
            } else if ($expense->couldBeUpdated()) {
                if ($expense->update($request->except(['eid','receipts']))) {
                   //todo: Receipts should be handling in another method-------------------!!!!!!
                    $feedback['code'] = 7;
                    $feedback['message'] = 'Record Update Success';
                    $feedback['record'] = ['company_paid'=>$expense->company_paid?'Yes':'No','total'=>number_format($expense->total(),2),
                        'receipts' => $expense->receipts, 'report_date' => $expense->report_date, 'description' => $expense->description,
                        'status' => $expense->getStatus()];
                } else {
                    $feedback['code'] = 4;
                    $feedback['message'] = 'unknown error during updating';
                }

            } else {
                $feedback['code'] = 1;
                $feedback['message'] = 'Record Cannot be updated now';
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
        $consultant = Auth::user()->entity;
        if ($request->ajax()) {

            $expense = Expense::find($id);
            //must check if this $expense record belong to the consultant!!!
            if ($expense && $expense->arrangement->consultant_id == $consultant->id) {
                if ($expense->couldBeDeleted()) {
                    if ($expense->delete()) return json_encode(['message' => 'succeed']);
                }
            }
            return json_encode(['message' => 'delete_failed']);
        }
    }
}
