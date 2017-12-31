@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title">Unapproved Time Reports</h3>
                    <p class="panel-subtitle">last paying period: <span class="badge bg-success">{{$confirm['startOfLast']->toFormattedDateString().' - '.$confirm['endOfLast']->toFormattedDateString()}}</span></p>
                </div>
                <div class="panel-body no-padding">
                    <div class="select-bp row">
                        <div class="col-md-3">
                            <a href="{{$type=='time'?'hour?me=1':'expense?me=1'}}" title="View Consultant Payroll"><img src="/img/my{{$type}}.png"
                                                                                   alt="Consultant Payroll"
                                                                                   width="90px"></a>
                            <br>
                            <p class="label label-{{$type=='time'?'primary':'info'}}">My Own Reports</p>
                        </div>
                        <div class="col-md-3 pull-right">
                            <a href="{{$type=='time'?'hour?team=1':'expense?me=0'}}" title="View Client Bill"><img src="/img/team{{$type}}.png" alt="Client Bill"
                                                                         width="90px"></a>
                            <br>
                            <p class="label label-success">Lead Engagements</p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <p><strong>NOTE: </strong>A REPORT SHALL BECOME <span class="label label-success">Approved</span> AFTER BEING CONFIRMED BOTH BY YOU AND THE ENGAGEMENT LEADER.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('special-css')
    <style>
        div.select-bp {
            margin: auto;
            width: 40%;
            padding: 97px 0;
            text-align: center;
        }

        div.select-bp img:hover {
            opacity: 0.5;
            filter: alpha(opacity=50);
        }
    </style>
@endsection
