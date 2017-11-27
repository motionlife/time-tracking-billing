@extends('layouts.html')
@section('special-css')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
@endsection
@section('wrapper')
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle">
                <div class="auth-box lockscreen clearfix" style=" margin-top: 7em">
                    <div class="content" style="background-color: #fff; color: #636b6f; font-family: 'Raleway', sans-serif;font-weight: 100;">
                        <h3>Registration succeed!</h3>
                           <img src="/img/processing-s.gif" alt='<i class="fa fa-hourglass-start" aria-hidden="true"></i>' style="margin-left: 7%;">

                        <h2>Hi! {{Auth::user()->first_name}}, we will send you an email as soon as the admin verifies
                            your application to use this system. </h2>

                    </div>
                    <p>But right now you can <a href="/profile">update your profile</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
