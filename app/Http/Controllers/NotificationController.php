<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }
    public function index()
    {
        return view('notification');
    }
}
