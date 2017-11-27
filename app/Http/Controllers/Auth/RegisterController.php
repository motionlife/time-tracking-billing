<?php

namespace newlifecfo\Http\Controllers\Auth;

use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Outreferrer;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\User;
use newlifecfo\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \newlifecfo\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role'=>$data['role']
        ]);
        $this->createRole($data, $user->id);
        return $user;
    }

    private function createRole($data, $uid)
    {
        $contact = Contact::create([
            'email'=>$data['email'],
            'phone'=>'123-2345-5434',
            'city'=>'Dallas',
            'state_id'=>51
        ]);
        switch ($data['role']) {
            case 3: //create consultant
                Consultant::create([
                    'user_id'=>$uid,
                    'contact_id'=>$contact->id,
                    'first_name'=>$data['first_name'],
                    'last_name' => $data['last_name'],
                    'standard_rate'=>0,
                    'standard_percentage'=>0
                ]);
                return 'Consultant';
            case 1:// create client
                    Client::create([
                        'user_id'=>$uid,
                        'contact_id'=>$contact->id,
                        'industry_id'=>1,
                        'name'=>'Unknown Client'
                    ]);
                return 'client';
            case 2://create outside referrer
                Outreferrer::create([
                    'user_id'=>$uid,
                    'contact_id'=>$contact->id,
                    'first_name'=>$data['first_name'],
                    'last_name' => $data['last_name'],
                ]);
                return 'Outside Referrer';
        }
    }
}
