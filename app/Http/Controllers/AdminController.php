<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use newlifecfo\Events\ConsultantRecognizedEvent;
use newlifecfo\Models\Client;
use newlifecfo\Models\Revenue;
use newlifecfo\Models\Templates\Contact;
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
                return $this->clientAdmin($request);
            case 'position':
                return $this->positionAdmin();
            case 'task':
                return $this->taskAdmin();
            case 'industry':
                return $this->industryAdmin();
        }
        return view('admin.miscellaneous');
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
                    if ($pre == 0) {
                        $target->delete();
                        $feedback['code'] = 7;
                    } else {
                        $feedback['code'] = 0;
                    }
                } else if ($request->get('action') == 'update') {
                    switch ($request->get('role')) {
                        case 0:
                            $target->priority = 0;
                            break;
                        case 1:
                            $target->priority = 1;
                            break;
                        case 2:
                            $target->priority = 3;
                            break;
                        case 3:
                            $target->priority = 5;
                            break;
                        case 4:
                            $target->priority = 11;
                            break;
                        case 5:
                            $target->priority = 51;
                            break;
                    }
                    if ($user->priority > $target->priority) {
                        if ($target->save()) {
                            $feedback['code'] = 7;
                            if ($pre == 0 && $target->priority > 1) {
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

    private function clientAdmin($request)
    {
        $feedback = 0;
        $client = $request->get('new') == 1;
        if ($request->get('edit') == 1 && is_numeric($request->get('cid'))) {
            $client = Client::find($request->get('cid'));
        }
        if ($request->isMethod('post')) {
            if ($request->get('action') == 'create') {
                $contact = Contact::create([
                    'email' => 'johndoe@example.com',
                    'phone' => '123-2345-5434',
                    'city' => 'Dallas',
                    'state_id' => 51
                ]);
                $client = (new Client(['contact_id' => $contact->id]))->fill($request->except('revenue2015', 'ebit2015', 'revenue2016', 'ebit2016', 'revenue2017', 'ebit2017', 'action'));
                if ($client->save()) {
                    if ($request->get('revenue2015') || $request->get('ebit2015')) $client->setRevenue(2015, $request->get('revenue2015'), $request->get('ebit2015'));
                    if ($request->get('revenue2016') || $request->get('ebit2016')) $client->setRevenue(2016, $request->get('revenue2016'), $request->get('ebit2016'));
                    if ($request->get('revenue2017') || $request->get('ebit2017')) $client->setRevenue(2017, $request->get('revenue2017'), $request->get('ebit2017'));
                    $feedback = 1;
                }
            } else if ($request->get('action') == 'update') {
                $client = Client::find($request->get('cid'));
                if ($client->update(['industry_id' => $request->get('industry_id'), 'buz_dev_person_id' => $request->get('buz_dev_person_id'), 'outreferrer_id' => $request->get('outreferrer_id'), 'name' => $request->get('name'),
                    'complex_structure' => $request->get('complex_structure'), 'messy_accounting_at_begin' => $request->get('messy_accounting_at_begin')
                ])) {
                    $client->setRevenue(2015, $request->get('revenue2015'), $request->get('ebit2015'));
                    $client->setRevenue(2016, $request->get('revenue2016'), $request->get('ebit2016'));
                    $client->setRevenue(2017, $request->get('revenue2017'), $request->get('ebit2017'));
                    $feedback = 1;
                }
            }
        }
        return view('admin.clients', ['client' => $client, 'feedback' => $feedback]);
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
