<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Client;
use newlifecfo\Models\Engagement;

class EngagementController extends Controller
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
        //
        $consultant = Auth::user()->entity;
        return view('engagement', ['engagements' => $consultant->myEngagements($request->get('start'), $request->get('cid'))]);
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
            if ($request->get('fetch') == 'business')
                return Client::find($request->get('cid'))->whoDevelopedMe();
        }
        $consultant = Auth::user()->entity;
        return view('engagement', ['engagements' => $consultant->my_lead_engagements($request->get('start'), $request->get('cid')),
            'leader' => $consultant]);
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
        $feedback = [];
        if ($request->ajax()) {
            $lid = $request->get('leader_id');
            if ($consultant->id == $lid) {
                $eng = new Engagement(['client_id' => $request->get('client_id'), 'leader_id' => $lid,
                    'name' => $request->get('name'), 'start_date' => $request->get('start_date'),
                    'buz_dev_share' => $request->get('buz_dev_share') / 100 ?: 0, 'paying_cycle' => $request->get('paying_cycle'),
                    'cycle_billing' => $request->get('cycle_billing') ?: 0
                ]);
                //only supervisor can touch the status
                if ($consultant->isSupervisor()) $eng->status = $request->get('status');
                else  $eng->status = 1;//indicate the engagement shall be pending once it created

                if ($eng->save()) {
                    if ($this->saveArrangements($request, $eng->id)) {
                        $feedback['code'] = 7;
                        $feedback['message'] = 'success';
                        $feedback['record'] = '';
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
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function saveArrangements(Request $request, $id)
    {
        foreach ($request->get('consultant_ids') as $i => $cid) {
            $pid = $request->get('position_ids')[$i];
            if ($cid && $pid) {
                if (!Arrangement::updateOrCreate(['engagement_id' => $id, 'consultant_id' => $cid, 'position_id' => $pid],
                    ['billing_rate' => $request->get('billing_rates')[$i] ?: 0, 'firm_share' => $request->get('firm_shares')[$i] / 100 ?: 0]))

                    return false;
            }
        }
        return true;
    }
}
