@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">

            <div class="panel panel-headline">
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel-heading">
                            <h3 class="panel-title">Time Reporting History</h3>
                            <p class="panel-subtitle">{{$hours->total()}} results</p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel-body">
                            <div class="col-md-5">
                                <label for="client-engagements">Client & Engagement</label>
                                <select class="selectpicker show-tick" data-width="auto" id="client-engagements" data-live-search="true">
                                    <option value="" selected>All</option>
                                    @foreach($clientIds as $cid=>$engagements)
                                        <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                                            @foreach($engagements as $eng)
                                                <option data-eid="{{$eng[0]}}" {{Request('eid')==$eng[0]?'selected':''}}>{{$eng[1]}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="start-date">From</label>
                                <input class="date-picker" id="start-date" placeholder="mm/dd/yyyy"
                                       value="{{Request('start')}}"
                                       type="text"/>
                                <label for="end-date">to</label>
                                <input class="date-picker" id="end-date" placeholder="mm/dd/yyyy"
                                       value="{{Request('end')}}" type="text"/>
                            </div>
                            <div class="col-md-1">
                                <a href="javascript:void(0)" type="button" class="btn btn-info" id="filter-button">Filter</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body no-padding">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Engagement</th>
                            <th>Client</th>
                            <th>Task</th>
                            <th>Billable Hours</th>
                            <th>Report Date</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Operate</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $offset = ($hours->currentPage() - 1) * $hours->perPage() + 1;?>
                        @foreach($hours as $hour)
                            <tr>
                                <th scope="row">{{$loop->index+$offset}}</th>
                                <td>{{str_limit($hour->arrangement->engagement->name,19)}}</td>
                                <td>{{str_limit($hour->arrangement->engagement->client->name,19)}}</td>
                                <td>{{str_limit($hour->task->getDesc(),23)}}</td>
                                <td><strong>{{number_format($hour->billable_hours,1)}}</strong></td>
                                <td>{{$hour->report_date}}</td>
                                <td>{{str_limit($hour->description,29)}}</td>
                                <td><span class="label label-{!!$hour->getStatus()[1].'">'.$hour->getStatus()[0]!!}</span></td>
                                <td><a href="#"><i class="fa fa-pencil-square-o"></i></a><a href="#"><i
                                                class="fa fa-times"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pull-right pagination">
                    {{ $hours->appends(Request::except('page'))->withPath('hour')->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('my-js')
    <script>
        $(function () {
            $('#filter-button').on('click', function () {
                var eid = $('#client-engagements').find(":selected").attr('data-eid');
                window.location.href = '/hour?eid=' + (eid ? eid : '') +
                    '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
            });
            $('.date-picker').datepicker(
                {
                    format: 'mm/dd/yyyy',
                    todayHighlight: true,
                    autoclose: true,
                }
            );
        });
    </script>
    <style>
        td a:nth-child(2) {
            padding-left: 1.5em;
            color: red;
        }
    </style>
@endsection()
