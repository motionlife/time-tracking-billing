<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;
use newlifecfo\User;

class AdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
        //todo: add isAdmin middleware here. !!!important
    }

    public function index($resource, Request $request)
    {
        switch ($resource) {
            case 'hour':
                return $this->hourEndorsement($request);
            case 'expense':
                return $this->expenseEndorsement($request);
            case 'user':
                return $this->userAdmin($request);
            case 'client':
                return $this->clientAdmin();
            case 'position':
                return $this->positionAdmin();
            case 'task':
                return $this->taskAdmin();
            case 'industry':
                return $this->industryAdmin();
        }
        return 'Resource Cannot Be Managed, No Authorization!';
    }

    private function userAdmin(Request $request)
    {

        if ($request->ajax()) {
            $feedback = [];
            $target = User::find($request->get('uid'));
            $user = Auth::user();
            if ($user->priority > $target->priority) {
                if ($request->get('action') == 'delete') {
                    $target->delete();
                    $feedback['code'] = 7;
                } else if ($request->get('action') == 'update') {
                    switch ($request->get('role')) {
                        case 0:
                            $target->priority = 0;
                            break;
                        case 1:
                            $target->priority = 1;
                            break;
                        case 2:
                            $target->priority = 11;
                            break;
                        case 3:
                            $target->priority = 51;
                            break;
                    }
                    if ($user->priority > $target->priority) {
                        $target->save();
                        $feedback['code'] = 7;
                    }
                }
            }
            return json_encode($feedback);
        } else {
            return view('admin.users');
        }

    }

    private function clientAdmin()
    {
        return view('admin.clients');
    }

    private function positionAdmin()
    {
    }

    private function taskAdmin()
    {
    }

    private function industryAdmin()
    {
    }

    private function hourEndorsement($request)
    {
        $hours = $this->paginate(Hour::recentReports($request->get('start'), $request->get('end'),
            $request->get('eid')), 25);
        $clientIds = Arrangement::all()->groupBy('engagement_id')
            ->mapToGroups(function ($item, $key) {
                $eng = Engagement::find($key);
                $cid = $eng->client->id;
                return [$cid => [$eng->id, $eng->name]];
            });
        return view('hours', ['hours' => $hours,
            'clientIds' => $clientIds]);
    }

    private function expenseEndorsement($request)
    {
        $consultant = Auth::user()->consultant;
        $expenses = $this->paginate(Expense::recentReports($request->get('start'),
            $request->get('end'), $request->get('eid')), 25);
        return view('expenses', ['expenses' => $expenses,
            'clientIds' => $consultant->myEngagementByClient()]);
    }
}
