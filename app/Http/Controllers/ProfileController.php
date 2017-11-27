<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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
        $user = Auth::user();
        $feedback = [];
        if ($request->ajax()) {
            if ($request->get('update') == 'account') {
                //user info update
                $user->first_name = $request->get('first_name');
                $user->last_name = $request->get('last_name');
                $password = $request->get('password');
                $password_confirmation = $request->get('password_confirmation');
                if (isset($password) || isset($password_confirmation)) {
                    if ($password_confirmation != $password) {
                        $feedback['code'] = 1;
                        $feedback['message'] = 'Password and Password Confirmation not the same.';
                        return $feedback;
                    }
                    if (strlen($password) < 6) {
                        $feedback['code'] = 2;
                        $feedback['message'] = 'Password must at least 6 characters.';
                        return $feedback;
                    }
                    $user->password = bcrypt($password);
                }
                if ($user->save()) {
                    $feedback['code'] = 7;
                    $feedback['message'] = 'success';
                } else {
                    $feedback['code'] = 3;
                    $feedback['message'] = 'An error happened during update';
                }
            } else if ($request->get('update') == 'business') {
                $consultant = $user->consultant;
                if ($consultant->update(['standard_rate' => $request->get('standard_rate'),
                    'standard_percentage' => $request->get('standard_percentage') / 100,
                    'isEmployee' => $request->get('isEmployee'), 'inactive' => $request->get('inactive')
                ])) {
                    $feedback['code'] = 7;
                    $feedback['message'] = 'success';
                };
            }
            return $feedback;
        }
        return view('profile');
    }
}
