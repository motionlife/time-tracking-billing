@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title">Confirm {{$report=='time'?'Time':'Expense'}} Reports</h3>
                    <p class="panel-subtitle">Last paying period: <span class="badge bg-success">{{$confirm['startOfLast']->toFormattedDateString().' - '.$confirm['endOfLast']->toFormattedDateString()}}</span></p>
                </div>
                <div class="panel-body no-padding">
                    <div class="select-bp row">
                        <div class="col-md-3">
                            <a href="{{$report=='time'?'hour?reporter=me':'expense?reporter=me'}}" title="Confirm my {{$report}} reports"><img src="/img/my{{$report}}.png"
                                                                                   alt="{{$report}}"
                                                                                   width="90px"><span class="badge bg-{{$confirm['count']['me']==0?'default':'danger'}}">{{$confirm['count']['me']}}</span></a>
                            <br>
                            <p class="label label-{{$report=='time'?'primary':'info'}}">My Own {{$report=='time'?'Hours':'Expenses'}}</p>

                        </div>
                        <div class="col-md-3 pull-right">
                            <a href="{{$report=='time'?'hour?reporter=team':'expense?reporter=team'}}" title="Approve my team's {{$report}} reports"><img src="/img/team{{$report}}.png" alt="{{$report}}"
                                                                         width="90px"><span class="badge bg-{{$confirm['count']['team']==0?'default':'danger'}}">{{$confirm['count']['team']}}</span></a>
                            <br>
                            <p class="label label-success">Led Engagements</p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <p><strong>NOTE: &nbsp;</strong>A REPORT WILL BE MARKED AS <span class="label label-success">Approved</span> AFTER BEING CONFIRMED BOTH BY YOU AND THE ENGAGEMENT LEADER.</p>
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
        div.select-bp span.badge {
            font-size: 1.2em;
            position: absolute;
            top: 1.7em;
            right: 5px;
        }
    </style>
@endsection
