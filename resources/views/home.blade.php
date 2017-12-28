@extends('layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title">Semi-monthly Overview</h3>
                    <p class="panel-subtitle"><a href="/payroll?state=1&start={{$data['dates']['startOfLast']->toDateString()}}&end={{$data['dates']['endOfLast']->toDateString()}}">
                            Period: {{$data['dates']['startOfLast']->toFormattedDateString('M d, Y')}}
                        - {{$data['dates']['endOfLast']->toFormattedDateString('M d, Y')}}</a></p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">{{number_format($data['total_last_b'],1)}}</span>
                                    <span class="title">Billable Hours</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-hourglass-start"></i></span>
                                <p>
                                    <span class="number">{{number_format($data['total_last_nb'],1)}}</span>
                                    <span class="title">Non-billable Hours</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-users"></i></span>
                                <p>
                                    <span class="number">{{count($data['eids'])}}</span>
                                    <span class="title">Engagement(s)</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-taxi"></i></span>
                                <p>
                                    <span class="number">${{number_format($data['last_expense'],2)}}</span>
                                    <span class="title">Expense</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <div id="semi-month-char" class="ct-chart"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="weekly-summary text-right">
                                <span class="number">${{number_format($data['last_buz_dev'],2)}}</span> <span
                                        class="percentage"><i
                                            class="fa fa-caret-{{$data['last_buz_dev']>$data['last2_buz_dev']?'up text-success':'down text-danger'}}"></i> {{$data['last2_buz_dev']?number_format(abs($data['last_buz_dev']/$data['last2_buz_dev']-1)*100,0):'-'}}
                                    %</span>
                                <span class="info-label">Business Developing Income</span>
                            </div>
                            <div class="weekly-summary text-right">
                                <span class="number">${{number_format($data['total_last_earn'],2)}}</span> <span
                                        class="percentage"><i
                                            class="fa fa-caret-{{$data['total_last_earn']>$data['total_last2_earn']?'up text-success':'down text-danger'}}"></i>
                                    {{$data['total_last2_earn']?number_format(abs($data['total_last_earn']/$data['total_last2_earn']-1)*100,0):'-'}}
                                    %</span>
                                <span class="info-label">Billable Hours Income</span>
                            </div>
                            <div class="weekly-summary text-right">
                                <?php
                                $last = $data['total_last_earn'] + $data['last_expense'] + $data['last_buz_dev'];
                                $last2 = $data['total_last2_earn'] + $data['last2_expense'] + $data['last2_buz_dev'];
                                ?>
                                <span class="number">${{number_format($last,2)}}</span>
                                <span class="percentage"><i
                                            class="fa fa-caret-{{$last>$last2?'up text-success':'down text-danger'}}"></i> {{$last2?number_format(abs($last/$last2-1)*100,0):'-'}}
                                    %</span>
                                <span class="info-label">Total Income</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Recent Reporting</h3>
                            <div class="right">
                                <button type="button" class="btn-toggle-collapse"><i class="lnr lnr-chevron-up"></i>
                                </button>
                                <button type="button" class="btn-remove"><i class="lnr lnr-cross"></i></button>
                            </div>
                        </div>
                        <div class="panel-body no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Engagement</th>
                                    <th>Client</th>
                                    <th>Billable Hours</th>
                                    <th>Report Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['recent_hours'] as $hour)
                                    <tr>
                                        <td>{{str_limit($hour->arrangement->engagement->name,15)}}</td>
                                        <td>
                                            <a href="#">{{str_limit($hour->arrangement->engagement->client->name,15)}}</a>
                                        </td>
                                        <td><strong>{{number_format($hour->billable_hours,1)}}</strong></td>
                                        <td>{{Carbon\Carbon::parse($hour->report_date)->format('M d, Y')}}</td>
                                        <td><span class="label label-{!!$hour->getStatus()[1].'">'.$hour->getStatus()[0]!!}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-md-6"><span class="panel-note"><i
                                                class="fa fa-clock-o"></i>{{$data['recent_hours']->count()?Carbon\Carbon::parse($data['recent_hours']->first()->report_date)->diffForHumans():''}}</span>
                                </div>
                                <div class="col-md-6 text-right"><a href="/hour" class="btn btn-primary">View All
                                        Reported</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Yearly Income vs. Hours</h3>
                            <div class="right">
                                <button type="button" class="btn-toggle-collapse"><i class="lnr lnr-chevron-up"></i>
                                </button>
                                <button type="button" class="btn-remove"><i class="lnr lnr-cross"></i></button>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div id="income-hours-chart" class="ct-chart"></div>
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
            var data, options;
            data = {
                labels: [
                    @foreach($data['last_earn'] as $day=>$earn)
                        '{{$day}}',
                    @endforeach
                ],
                series: [[
                    @foreach($data['last_earn'] as $earn)
                        '{{$earn}}',
                    @endforeach
                ], [@foreach($data['last_b'] as $b)
                    '{{$b*15}}',
                    @endforeach]]
            };
            options = {
                height: 300,
                showArea: true,
                showLine: false,
                showPoint: false,
                fullWidth: true,
                axisX: {
                    showGrid: false
                }
            };
            new Chartist.Line('#semi-month-char', data, options);

            data = {
                labels: [@foreach($data['dates']['mon'] as $key=>$month)
                    '{{$key}}',
                    @endforeach],
                series: [{
                    name: 'series-income',
                    data: [@foreach($data['dates']['mon'] as $amount)
                        '{{$amount[1]}}',
                        @endforeach],
                }, {
                    name: 'series-hours',
                    data: [@foreach($data['dates']['mon'] as $amount)
                        '{{$amount[0]*15}}',
                        @endforeach],
                }]
            };
            options = {
                fullWidth: true,
                height: "270px",
                low: 0,
                high: 'auto',
                series: {
                    'series-income': {
                        showArea: true,
                        showPoint: false,
                        showLine: false
                    },
                },
                axisX: {
                    showGrid: false,
                    offset: 14
                },
                axisY: {
                    showGrid: true,
                    onlyInteger: true,
                    offset: 19
                },
                chartPadding: {
                    left: 20,
                    right: 20
                }
            };
            new Chartist.Line('#income-hours-chart', data, options);
        });
    </script>
@endsection