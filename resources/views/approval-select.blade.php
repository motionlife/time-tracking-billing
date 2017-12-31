@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title">Unapproved Time Report</h3>
                    <p class="panel-subtitle">last paying period: <span class="label label-info">{{$confirm['startOfLast']->toFormattedDateString().' - '.$confirm['endOfLast']->toFormattedDateString()}}</span></p>
                </div>
                <div class="panel-body no-padding">
                    <div class="select-bp row">
                        <div class="col-md-3">
                            <a href="payroll" title="View Consultant Payroll"><img src="/img/payroll.png"
                                                                                   alt="Consultant Payroll"
                                                                                   width="90px"></a>
                            <br>
                            <p class="label label-success">My Own Reports</p>
                        </div>
                        <div class="col-md-3 pull-right">
                            <a href="bill" title="View Client Bill"><img src="/img/billing.png" alt="Client Bill"
                                                                         width="90px"></a>
                            <br>
                            <p class="label label-success">Teammates' Reports</p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <p>Note: A report shall become 'approved' only when being confirmed both by you and your leader!</p>
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
