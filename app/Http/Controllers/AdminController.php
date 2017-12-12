<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\User;

class AdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('supervisor');
    }

    public function index($resource, Request $request)
    {
        switch ($resource) {
            case 'hour':
                return $this->hourEndorsement($request);
            case 'expense':
                return $this->expenseEndorsement($request);
            case 'engagement':
                return $this->grantEngagement($request);
            case 'bp':
            case 'payroll':
            case 'billing':
                return $this->viewBp($request);
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
        return app(HoursController::class)->index($request, true);
    }

    private function expenseEndorsement($request)
    {
        return app(ExpenseController::class)->index($request, true);
    }

    private function grantEngagement($request)
    {
        return app(EngagementController::class)->index($request, true);
    }

    private function viewBp($request)
    {
        return app(PayrollController::class)->index($request, true);
    }

}
