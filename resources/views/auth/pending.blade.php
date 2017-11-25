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
                        <img src="/img/processing-s.gif" alt="">
                        
                        <h2>Hi! {{Auth::user()->first_name}}, we will send you an email as soon as the admin verifies
                            your application to use this system. </h2>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
