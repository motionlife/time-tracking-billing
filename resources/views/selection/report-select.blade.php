@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title">Consultants' Time and Expense Reports</h3>
                    <p class="panel-subtitle">Click the icon to admin reports</p>
                </div>
                <div class="panel-body no-padding">
                    <div class="select-bp row">
                        <div class="col-md-3">
                            <a href="hour" title="Admin All Time Reports"><img src="/img/mytime.png"
                                                                                   alt="hours"
                                                                                   width="90px"></a>
                            <br>
                            <p class="label label-primary">Admin Time Report</p>
                        </div>
                        <div class="col-md-3 pull-right">
                            <a href="expense" title="Admin All Expense Reports"><img src="/img/myexpense.png" alt="Client Bill"
                                                                         width="90px"></a>
                            <br>
                            <p class="label label-info">Admin Expense Report</p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <p><strong>NOTE: </strong>MODIFYING CONSULTANTS' REPORT DATA MAY AFFECT THEIR INCOMES AND CLIENTS' BILLS. PLEASE PROCEED WITH CAUTION!</p>
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
        div.panel-footer strong{
            color:red;
        }
    </style>
@endsection
