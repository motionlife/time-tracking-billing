<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
        //todo: add isAdmin middleware
    }

    public function index($id)
    {
        switch ($id) {
            case 'users':
                return $this->userAdmin();
            case 'clients':
                return $this->clientAdmin();
            case 'positions':
                return $this->positionAdmin();
            case 'tasks':
                return $this->taskAdmin();
            case 'industries':
                return $this->industryAdmin();
        }
        return 'Cannot be managed';
    }

    private function userAdmin()
    {



        return view('admin.users');
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
}
