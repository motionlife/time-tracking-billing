@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="select-bp row">
                <div class="col-md-3">
                    <a href="payroll" title="View Consultant Payroll"><img src="/img/payroll.png"
                                                                                 alt="Consultant Payroll" width="90px"></a>
                    <br>
                    <p class="label label-success">Consultant Payroll</p>
                </div>
                <div class="col-md-3 pull-right">
                    <a href="bill" title="View Client Bill"><img src="/img/billing.png" alt="Client Bill"
                                                                       width="90px"></a>
                    <br>
                    <p class="label label-success">Client Bill</p>
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
