@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="row">
                    <div class="panel-heading col-md-3">
                        <h3 class="panel-title">{{$admin?(isset($consultant)?$consultant->fullname()."'s Payroll":'All Payrolls'):'My Payroll'}}</h3>
                        <p class="panel-subtitle">
                            Period: {{(Request::get('start')?:'Begin of time').' - '.(Request::get('end')?:'Today')}}</p>
                    </div>
                    <div class="panel-body col-md-9">
                        @component('components.filter',['clientIds'=>$clientIds,'admin'=>$admin,'target'=>'payroll'])
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
                                <span class="icon"><i class="fa fa-taxi"></i></span>
                                <p>
                                    <span class="number">${{number_format($income[1],2)}}</span>
                                    <span class="title">Expenses</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-handshake-o"></i></span>
                                <div class="dev-closings">
                                    <p>
                                        <span class="number">${{number_format($buz_devs['total'],2)}}</span>
                                        <span class="title">Biz Dev Income</span>
                                    </p>
                                    <p>
                                        <span class="number">${{number_format($closings['total'],2)}}</span>
                                        <span class="title">Closings</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-calculator"></i></span>
                                <p>
                                        <span class="number"
                                              id="total-income-tag">${{number_format($income[0]+$income[1]+$buz_devs['total']+$closings['total'],2)}}</span>
                                    <span class="title">Total Payroll</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 1.5em;padding-right: 1.5em;">
                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                            <ul class="nav" role="tablist" id="top-tab-nav">
                                @if(isset($consultant))
                                    @php $activeTab = Request::get('tab')?:"1"; @endphp
                                    <li class="{{$activeTab=="1"?'active':''}}"><a href="#tab-left1" role="tab"
                                                                                   data-toggle="tab">Hourly Income&nbsp;<span
                                                    class="badge bg-success">{{$hours->total()}}</span></a></li>
                                    <li class="{{$activeTab=="2"?'active':''}}"><a href="#tab-left2" role="tab"
                                                                                   data-toggle="tab">Expense&nbsp;<span
                                                    class="badge bg-warning">{{$expenses->total()}}</span></a>
                                    </li>
                                    <li class="{{$activeTab=="3"?'active':''}}"><a href="#tab-left3" role="tab"
                                                                                   data-toggle="tab">Biz Dev Income&nbsp;<span
                                                    class="badge bg-danger">{{sizeof($buz_devs['engs'])}}</span></a>
                                    </li>
                                    <li class="{{$activeTab=="4"?'active':''}}"><a href="#tab-left4" role="tab"
                                                                                   data-toggle="tab">Engagement Closing&nbsp;<span
                                                    class="badge bg-info">{{sizeof($closings['engs'])}}</span></a>
                                    </li>
                                @else
                                    <li class="active"><a href="#tab-left1" role="tab"
                                                          data-toggle="tab">All Consultants Payroll&nbsp;<span
                                                    class="badge bg-success">{{$consultants->count()}}</span></a></li>
                                @endif
                            </ul>
                            <div class="pull-right excel-button"><a
                                        href="{{str_replace_first('/','',route('payroll',array_add(Request::all(),'file','excel'),false))}}"
                                        type="button" title="Download excel file"><img src="/img/excel.png" alt=""></a>
                            </div>
                        </div>
                        @if(isset($consultant))
                            <div class="tab-content">
                                <div class="tab-pane fade {{$activeTab=="1"?' in active':''}}" id="tab-left1">
                                    <div class="table-responsive">
                                        <table class="table project-table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Engagement<a
                                                            href="{{url()->current().'?'.http_build_query(Request::except('eid','page'))}}">&nbsp;<i
                                                                class="fa fa-refresh" aria-hidden="true"></i></a></th>
                                                <th>Report Date</th>
                                                <th>Billable Hours</th>
                                                <th>Non-billable Hours</th>
                                                <th>Pay Rate</th>
                                                {{--03/05/2018 Diego changed to show only the pay rate--}}
                                                {{--<th>Rate</th>--}}
                                                {{--<th>Share</th>--}}
                                                <th>Income</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $offset = ($hours->currentPage() - 1) * $hours->perPage() + 1;?>
                                            @foreach($hours as $hour)
                                                @php
                                                    $eng = $hour->arrangement->engagement;
                                                @endphp
                                                <tr>
                                                    <th scope="row">{{$loop->index+$offset}}</th>
                                                    <td>{{str_limit($hour->client->name,20)}}</td>
                                                    <td>
                                                        <a href="{{str_replace_first('/','',route('payroll',array_add(Request::except('eid','tab','page'),'eid',$eng->id),false))}}">{{str_limit($eng->name,20)}}</a>
                                                    </td>
                                                    <td>{{$hour->report_date}}</td>
                                                    <td>{{number_format($hour->billable_hours,2)}}</td>
                                                    <td>{{number_format($hour->non_billable_hours,2)}}</td>
                                                    {{--03/05/2018 Diego changed to show only the pay rate--}}
                                                    <td>
                                                        ${{number_format($hour->rate * $hour->share,2)}}
                                                    </td>
                                                    {{--<td>{{number_format($hour->share*100,1)}}%</td>--}}
                                                    <td>
                                                        ${{number_format($hour->earned(),2)}}</td>
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
                                                <th>Engagement<a
                                                            href="{{url()->current().'?'.http_build_query(Request::except('eid','page'))}}">&nbsp;<i
                                                                class="fa fa-refresh" aria-hidden="true"></i></a></th>
                                                <th>Report Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $offset = ($expenses->currentPage() - 1) * $expenses->perPage() + 1;?>
                                            @foreach($expenses as $expense)
                                                @php
                                                    $eng = $expense->arrangement->engagement;
                                                @endphp
                                                <tr>
                                                    <th scope="row">{{$loop->index+$offset}}</th>
                                                    <td>{{str_limit($expense->client->name,30)}}</td>
                                                    <td>
                                                        <a href="{{str_replace_first('/','',route('payroll',array_add(Request::except('eid','tab','page'),'eid',$eng->id),false)).'&tab=2'}}">{{str_limit($eng->name,30)}}</a>
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
                                                <th>Engagement<a
                                                            href="{{url()->current().'?'.http_build_query(Request::except('eid','page'))}}">&nbsp;<i
                                                                class="fa fa-refresh" aria-hidden="true"></i></a></th>
                                                <th>ENG. Status</th>
                                                <th>Business Development Share</th>
                                                <th>Engagement Bill</th>
                                                <th>Earned</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($buz_devs['engs'] as $eng)
                                                <tr>
                                                    <td>{{$loop->index+1}}</td>
                                                    <td>{{str_limit($eng[0]->client->name,32)}}</td>
                                                    <td>
                                                        <span class="badge bg-{{$eng[0]->paying_cycle==0?'default':($eng[0]->paying_cycle==1?'warning':'danger')}}">{{$eng[0]->paying_cycle==0?'H':($eng[0]->paying_cycle==1?'M':'Fixed')}}</span>
                                                        <a href="{{str_replace_first('/','',route('payroll',array_add(Request::except('eid','tab','page'),'eid',$eng[0]->id),false)).'&tab=3'}}">{{str_limit($eng[0]->name,32)}}</a>
                                                    </td>
                                                    <td>
                                                        <span class="label label-{{$eng[0]->getStatusLabel()}}">{{$eng[0]->state()}} </span>
                                                    </td>
                                                    <td>
                                                        @php $share = number_format($eng[0]->buz_dev_share*100,1) @endphp
                                                        <div class="progress">
                                                            <div class="progress-bar progress-bar-success"
                                                                 role="progressbar" aria-valuenow="{{$share}}"
                                                                 aria-valuemin="0" aria-valuemax="100"
                                                                 style="width:{{$share}}%;">
                                                                <span>{{$share}}%</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><a class="label label-info" data-toggle="popover"
                                                           data-content="{{$eng[0]->summaryForBuzDev($eng[2])}}">
                                                            ${{number_format($eng[2],2)}}</a></td>
                                                    <td>${{number_format($eng[1],2)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade {{$activeTab=="4"?' in active':''}}" id="tab-left4">
                                    <div class="table-responsive">
                                        <table class="table project-table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Engagement<a
                                                            href="{{url()->current().'?'.http_build_query(Request::except('eid','page'))}}">&nbsp;<i
                                                                class="fa fa-refresh" aria-hidden="true"></i></a></th>
                                                <th>ENG. Status</th>
                                                <th>Closing Share</th>
                                                <th>Effective Period</th>
                                                <th>Period Billing</th>
                                                <th>Commission</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($closings['engs'] as $eng)
                                                <tr>
                                                    <td>{{$loop->index+1}}</td>
                                                    <td>{{str_limit($eng[0]->client->name,32)}}</td>
                                                    <td>
                                                        <span class="badge bg-{{$eng[0]->paying_cycle==0?'default':($eng[0]->paying_cycle==1?'warning':'danger')}}">{{$eng[0]->paying_cycle==0?'H':($eng[0]->paying_cycle==1?'M':'Fixed')}}</span>
                                                        <a href="{{str_replace_first('/','',route('payroll',array_add(Request::except('eid','tab','page'),'eid',$eng[0]->id),false)).'&tab=4'}}">{{str_limit($eng[0]->name,32)}}</a>
                                                    </td>
                                                    <td>
                                                        <span class="label label-{{$eng[0]->getStatusLabel()}}">{{$eng[0]->state()}} </span>
                                                    </td>
                                                    <td>{{number_format($eng[0]->closer_share*100,1)}}%</td>
                                                    <td>{{\Carbon\Carbon::parse($eng[0]->closer_from)->format('M d, Y')}}
                                                        - {{\Carbon\Carbon::parse($eng[0]->closer_end)->format('M d, Y')}}</td>
                                                    <td><a class="label label-info" data-toggle="popover"
                                                           data-content="{{$eng[0]->summaryForBuzDev($eng[2])}}">${{number_format($eng[2],2)}}</a>
                                                    </td>
                                                    <td>${{number_format($eng[1],2)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Billable Hours</th>
                                    <th>Non-billable Hours</th>
                                    <th>Hourly Income</th>
                                    <th>Expense</th>
                                    <th>Biz Dev Income</th>
                                    <th>Closings</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody id="summary">
                                @php $index =0; @endphp
                                @foreach($consultants as $consultant)
                                    @php $conid=$consultant->id;$salary = $incomes[$conid];$total = $salary[0]+$salary[1]+$buzIncomes[$conid]+$closerIncomes[$conid];@endphp
                                    @if($total>0.01)
                                        <tr>
                                            <td>{{++$index}}</td>
                                            <td>
                                                <a href="{{str_replace_first('/','',route('payroll',array_add(Request::except('conid'),'conid',$consultant->id),false))}}">{{$consultant->fullname()}}</a>
                                            </td>
                                            <td>{{$hrs[$conid][0]}}</td>
                                            <td>{{$hrs[$conid][1]}}</td>
                                            <td>{{$salary[0]?'$'.number_format($salary[0],2):'-'}}</td>
                                            <td>{{$salary[1]?'$'.number_format($salary[1],2):'-'}}</td>
                                            <td>{{$buzIncomes[$conid]?'$'.number_format($buzIncomes[$conid],2):'-'}}</td>
                                            <td>
                                                {{$closerIncomes[$conid]?'$'.number_format($closerIncomes[$conid],2):'-'}}</td>
                                            <td>${{number_format($total,2)}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @endif
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
            $('[data-toggle="popover"]').popover({
                html: true,
                container: '.main-content',
                placement: 'top',
                trigger: 'hover'
            });
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

        #tab-left1 tr td:nth-child(8) {
            font-weight: bold;
            font-size: 14px;
        }

        #total-income-tag {
            font-weight: normal;
        }

        #tab-left3 tr td:last-child {
            font-weight: bold;
        }

        #tab-left4 tr td:last-child {
            font-weight: bold;
        }

        #summary tr td:nth-last-child(-n+5) {
            font-weight: 600;
            font-size: 1.1em;
        }

        .excel-button:hover {
            opacity: 0.5;
            filter: alpha(opacity=50);
        }

        .metric span.number {
            font-size: 1.6em;
        }

        .metric .dev-closings .number {
            font-size: 1.3em;
        }

        .metric .dev-closings .title {
            font-size: 0.8em;
        }

        .metric .dev-closings {
            margin-top: -1.2em;
            margin-bottom: -1.2em;
        }
    </style>
@endsection
