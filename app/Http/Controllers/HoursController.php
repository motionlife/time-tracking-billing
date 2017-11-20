<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoursController extends Controller
{
    /**
     * Create a new controller instance.
     * Hour Controller
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $consultant = Auth::user()->entity;
        if($request->ajax()&&$request->get('fetch')=='position')
            return $consultant->getPositionsByEid($request->get('eid'));

        $hours = $this->paginate($consultant->recentHourReports($request->get('start'),
            $request->get('end'), $request->get('eid')), 25);
        return view('hours', ['hours' => $hours,
            'clientIds' => $consultant->EngagementByClient()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //today's report
        $consultant = Auth::user()->entity;
        return view('new-hour', ['clientIds' => $consultant->EngagementByClient()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //same reported hours

        if ($request->ajax()) {
            sleep(1.2);
        return $request->all();
        } else {

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
}
