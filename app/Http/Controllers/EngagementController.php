<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Client;
use newlifecfo\Models\Engagement;
use newlifecfo\User;

class EngagementController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $consultant = Auth::user()->consultant;
        return view('engagement', ['engagements' => $consultant->myEngagements($request->get('start'), $request->get('cid')),
            'cids' => $consultant->myEngagements()->pluck('client_id')->unique()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        if ($request->ajax()) {
            //return the business development info to the request
            if ($request->get('fetch') == 'business')
                return Client::find($request->get('cid'))->whoDevelopedMe();
        }
        $consultant = Auth::user()->consultant;
        return view('engagement', ['engagements' => $consultant->my_lead_engagements($request->get('start'), $request->get('cid')),
            'leader' => $consultant, 'cids' => $consultant->lead_engagements->pluck('client_id')->unique()]);
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
        //$user = Auth::user()
        $consultant = Auth::user()->consultant;
        $feedback = [];
        if ($request->ajax()) {
            $lid = $request->get('leader_id');
            if ($consultant->id == $lid) {
                $eng = new Engagement(['client_id' => $request->get('client_id'), 'leader_id' => $lid,
                    'name' => $request->get('name'), 'start_date' => $request->get('start_date'),
                    'buz_dev_share' => $request->get('buz_dev_share') / 100 ?: 0, 'paying_cycle' => $request->get('paying_cycle'),
                    'cycle_billing' => $request->get('cycle_billing') ?: 0, 'status' => 1
                ]);
                //only supervisor can touch the status(no need to apply policy here)
                /* if ($this->authorize('activate', $eng) || $this->authorize('close', $eng))
                     $eng->status = $request->get('status');
                 else  $eng->status = 1;//indicate the engagement shall be pending once it created*/

                if ($eng->save()) {
                    if ($this->saveArrangements($request, $eng->id)) {
                        $feedback['code'] = 7;
                        $feedback['message'] = 'success';
                    } else {
                        $eng->delete();
                        $feedback['code'] = 2;
                        $feedback['message'] = 'Saving engagement failed, unsupported data encountered!';
                    }

                } else {
                    $feedback['code'] = 1;
                    $feedback['message'] = 'Saving engagement failed, there may be some unsupported data';
                }
            } else {
                $feedback['code'] = 0;
                $feedback['message'] = 'Unauthorized Operation';
            }
        }

        return json_encode($feedback);
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
            $eng = Engagement::find($id);
            if ($user->can('view', $eng)) {
                foreach ($eng->arrangements as $arrangement) {
                    if(!$user->can('view',$arrangement)){
                        $arrangement->billing_rate='';
                        $arrangement->firm_share='';
                    }
                }
                return $eng;
            }
            //else illegal request! todo: some feedback
        } else {
            return "Illegeal Request!";
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $feedback = [];
        if ($request->ajax()) {
            //business logic validation is important
            //1. check the if the reported engagement is his valid engagement
            $eng = Engagement::find($id);
            if ($user->can('update', $eng)) {
                //should not let normal user update their own status!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                if ($eng->update(['client_id' => $request->get('client_id'),
                    'name' => $request->get('name'), 'start_date' => $request->get('start_date'),
                    'buz_dev_share' => $request->get('buz_dev_share') / 100 ?: 0, 'paying_cycle' => $request->get('paying_cycle'),
                    'cycle_billing' => $request->get('cycle_billing') ?: 0
                ])) {
                    //only manager or superAdmin can touch the status
                    if ($user->can('activate', $eng)) $eng->update(['status' => $request->get('status') ?: 1]);
                    if ($this->updateArrangements($request, $eng)) {
                        $feedback['code'] = 7;
                        $feedback['message'] = 'Record Update Success';
                    } else {
                        $feedback['code'] = 6;
                        $feedback['message'] = 'Updating arrangements failed, engagement update rollback';
                    }
                } else {
                    $feedback['code'] = 4;
                    $feedback['message'] = 'unknown error during updating';
                }

            } else {
                $feedback['code'] = 1;
                $feedback['message'] = 'Active engagement can only be updated by manager';
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

            $eng = Engagement::find($id);
            //must check if this $expense record belong to the consultant!!!
            if ($user->can('delete', $eng)) {
                foreach ($eng->arrangements as $arrangement) {
                    $arrangement->delete();
                }
                if ($eng->delete()) {
                    return json_encode(['message' => 'succeed']);
                } else {
                    return json_encode(['message' => 'Can\'t delete this Active engagement']);
                }
            }
            return json_encode(['message' => ' No authorization']);
        }
    }

    private function saveArrangements(Request $request, $id)
    {
        $pids = $request->get('position_ids');
        $fs = $request->get('firm_shares');
        $bs = $request->get('billing_rates');
        foreach ($request->get('consultant_ids') as $i => $cid) {
            if ($cid && $pids[$i]) {
                if (!Arrangement::updateOrCreate(['engagement_id' => $id, 'consultant_id' => $cid, 'position_id' => $pids[$i]],
                    ['billing_rate' => $bs[$i] ?: 0, 'firm_share' => $fs[$i] / 100 ?: 0]))

                    return false;
            }
        }
        return true;
    }

    private function updateArrangements(Request $request, $eng)
    {
        $cids = $request->get('consultant_ids');
        $pids = $request->get('position_ids');
        $fs = $request->get('firm_shares');
        $bs = $request->get('billing_rates');
        foreach ($eng->arrangements as $arr) {
            $i = array_search($arr->consultant_id, $cids);
            if ($i == false) {
                //delete the removed consultant
                //soft delete indicates the consultant has been removed from his original engagement
                $arr->delete();
            }
        }
        //add new one if exist and update the old guys
        foreach ($cids as $i => $cid) {
            if (!Arrangement::updateOrCreate(
                ['engagement_id' => $eng->id, 'consultant_id' => $cid],
                ['billing_rate' => $bs[$i] ?: 0, 'firm_share' => $fs[$i] / 100 ?: 0, 'position_id' => $pids[$i]]
            )) {
                return false;
            }
        }
        return true;
    }
}
