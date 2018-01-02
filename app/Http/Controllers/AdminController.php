<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Events\ConsultantRecognizedEvent;
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
            case 'report':
            case 'hour':
            case 'expense':
                return $this->adminReport($request, $resource);
            case 'engagement':
                return $this->grantEngagement($request);
            case 'bp':
            case 'payroll':
            case 'bill':
                return $this->viewBp($request, $resource);
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
                $pre = $target->priority;
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
                        if ($target->save()) {
                            $feedback['code'] = 7;
                            if ($pre == 0 && $target->priority > 0) {
                                //fire the event to notify user ready to use
                                event(new ConsultantRecognizedEvent($target));
                            }

                        }
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

    private function adminReport($request, $resource)
    {
        if ($resource == 'report') {
            return view('selection.report-select');
        } else if ($resource == 'hour') {
            return app(HoursController::class)->index($request, true);
        } else if ($resource == 'expense') {
            return app(ExpenseController::class)->index($request, true);

        }
        abort(404);
    }

    private function grantEngagement($request)
    {
        return app(EngagementController::class)->index($request, true);
    }

    private function viewBp($request, $resource)
    {
        if ($resource == 'bp') {
            return view('selection.bp-select');
        } else {
            return app(AccountingController::class)->index($request, true, $resource == 'payroll');
        }
    }

}
