@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">

            <div class="panel panel-headline">
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel-heading">
                            <h3 class="panel-title">Time Reporting History</h3>
                            <p class="panel-subtitle">{{$hours->total()}}</p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel-body">
                            <div class="col-md-5">
                                <label for="client-engagements">Client & Engagement</label>
                                <select class="selectpicker" id="client-engagements">
                                    <option value="" selected>All</option>
                                    @foreach($clientIds as $cid=>$engagements)
                                        <optgroup label="{{newlifecfo\Models\Client::find($cid)->name}}">
                                            @foreach($engagements as $eng)
                                                <option id="{{$eng[0]}}" {{Request('eid')==$eng[0]?'selected':''}}>{{$eng[1]}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="start-date">From</label>
                                <input type="date" id="start-date" value="{{Request('start')}}">
                                <label for="end-date">to</label>
                                <input type="date" id="end-date" value="{{Request('end')}}">
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
                                <td><span class="label label-{!!$hour->review_state?'sucess">Approved':'warning">Pending'!!}</span></td>
                                        <td><a href=" #">Edit</a></td>
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
                var eid = $('#client-engagements').find(":selected").attr('id');
                window.location.href = '/hour?eid=' + (eid ? eid : '') +
                    '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
            })
        });
    </script>
@endsection()
