@extends('layouts.app')
@section('content')
    @php $admin = true; @endphp
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="row">
                    <div class="panel-heading col-md-3">
                        <h3 class="panel-title">Estimated Payroll</h3>
                        <p class="panel-subtitle">
                            Period: {{(Request::get('start')?:'Begin of time').' - '.(Request::get('end')?:'Today')}}</p>
                    </div>
                    <div class="panel-body col-md-9">
                        @component('components.filter',['clientIds'=>$clientIds,'admin'=>$admin])
                        @endcomponent
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">${{number_format($income[0],2)}}</span>
                                    <span class="title">Hourly Income</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">${{number_format($income[1],2)}}</span>
                                    <span class="title">Expenses</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">${{number_format($buz_devs['total'],2)}}</span>
                                    <span class="title">Business Development</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-money"></i></span>
                                <p>
                                    <span class="number" id="total-income-tag">${{number_format($income[0]+$income[1]+$buz_devs['total'],2)}}</span>
                                    <span class="title">Total Payroll</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 1.5em;padding-right: 1.5em;">
                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                            <ul class="nav" role="tablist" id="top-tab-nav">
                                @php $activeTab = Request::get('tab')?:"1"; @endphp
                                <li class="{{$activeTab=="1"?'active':''}}"><a href="#tab-left1" role="tab"
                                                                               data-toggle="tab">Hourly
                                        Income&nbsp;<span
                                                class="badge bg-success">{{$hours->total()}}</span></a></li>
                                <li class="{{$activeTab=="2"?'active':''}}"><a href="#tab-left2" role="tab"
                                                                               data-toggle="tab">Expense&nbsp;<span
                                                class="badge bg-warning">{{$expenses->total()}}</span></a>
                                </li>
                                <li class="{{$activeTab=="3"?'active':''}}"><a href="#tab-left3" role="tab"
                                                                               data-toggle="tab">Buz Dev
                                        Income&nbsp;<span
                                                class="badge bg-danger">{{sizeof($buz_devs['engs'])}}</span></a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade {{$activeTab=="1"?' in active':''}}" id="tab-left1">
                                <div class="table-responsive">
                                    <table class="table project-table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Client</th>
                                            <th>Engagement</th>
                                            <th>Report Date</th>
                                            <th>Billable Hours</th>
                                            <th>Income</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $offset = ($hours->currentPage() - 1) * $hours->perPage() + 1;?>
                                        @foreach($hours as $hour)
                                            @php
                                                $arr = $hour->arrangement;
                                                $eng = $arr->engagement;
                                                $cname =$arr->consultant->fullname();
                                            @endphp
                                            <tr>
                                                <th scope="row">{{$loop->index+$offset}}</th>
                                                <td>{{str_limit($eng->client->name,30)}}</td>
                                                <td><a href="{{route('payroll',array_add(Request::except('eid'),'eid',$eng->id))}}">{{str_limit($eng->name,30)}}</a></td>
                                                <td>{{$hour->report_date}}</td>
                                                <td>{{number_format($hour->billable_hours,1)}}</td>
                                                <td>
                                                    ${{number_format($hour->billable_hours * $arr->billing_rate * (1 - $arr->firm_share),2)}}</td>
                                                <td>
                                                    <span class="label label-{{$hour->getStatus()[1]}}">{{$hour->getStatus()[0]}}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-right pagination">
                                    {{$hours->appends(Request::except('page','tab'))->withPath('payroll')->links()}}
                                </div>
                            </div>

                            <div class="tab-pane fade {{$activeTab=="2"?' in active':''}}" id="tab-left2">
                                <div class="table-responsive">
                                    <table class="table project-table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Client</th>
                                            <th>Engagement</th>
                                            <th>Report Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $offset = ($expenses->currentPage() - 1) * $expenses->perPage() + 1;?>
                                        @foreach($expenses as $expense)
                                            @php
                                                $arr = $expense->arrangement;
                                                $eng = $arr->engagement;
                                                $cname =$arr->consultant->fullname();
                                            @endphp
                                            <tr>
                                                <th scope="row">{{$loop->index+$offset}}</th>
                                                <td>{{str_limit($eng->client->name,30)}}</td>
                                                <td><a href="?eid={{$eng->id}}&tab=2">{{str_limit($eng->name,30)}}</a>
                                                </td>
                                                <td>{{$expense->report_date}}</td>
                                                <td>${{number_format($expense->total(),2)}}</td>
                                                <td>
                                                    <span class="label label-{{$expense->getStatus()[1]}}">{{$expense->getStatus()[0]}}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-right pagination">
                                    {{$expenses->appends(array_add(Request::except('page'),'tab',2))->withPath('payroll')->links()}}
                                </div>
                            </div>

                            <div class="tab-pane fade {{$activeTab=="3"?' in active':''}}" id="tab-left3">
                                <div class="table-responsive">
                                    <table class="table project-table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Client</th>
                                            <th>Engagement</th>
                                            <th>Status</th>
                                            <th>Business Development Share</th>
                                            <th>Earned</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($buz_devs['engs'] as $eng)
                                            <tr>
                                                <td>{{$loop->index+1}}</td>
                                                <td>{{str_limit($eng[0]->client->name,32)}}</td>
                                                <td><a href="?eid={{$eng[0]->id}}&tab=3">{{str_limit($eng[0]->name,32)}}</a>
                                                <td><span class="label label-{{$eng[0]->getStatusLabel()}}">{{$eng[0]->state()}}</span></td>
                                                <td>
                                                    @php $share = number_format($eng[0]->buz_dev_share*100,1) @endphp
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$share}}"
                                                             aria-valuemin="0" aria-valuemax="100" style="width:{{$share}}%;">
                                                            <span>{{$share}}%</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>${{number_format($eng[1],2)}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('my-js')
    <script>
        $(function () {
            $('.date-picker').datepicker(
                {
                    format: 'mm/dd/yyyy',
                    todayHighlight: true,
                    autoclose: true
                }
            );
        });

    </script>
@endsection

@section('special-css')
    <style>
        .tab-content tr td:nth-child(5) {
            font-weight: bold;
        }

        #tab-left1 tr td:nth-child(5) {
            text-indent: 1.2em;
        }

        #tab-left1 tr td:nth-child(6) {
            font-weight: bold;
            font-size: 14px;
        }
        #total-income-tag{
            font-weight: normal;
        }
        #tab-left3 tr td:last-child{
            font-weight: bold;
        }
    </style>
@endsection
