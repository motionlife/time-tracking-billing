@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h3> Time Reporting History</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Engagement</th>
                                <th>Billable Hours</th>
                                <th>Non-billable Hours</th>
                                <th>Report Date</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($hours as $hour)
                                <tr>
                                    <th scope="row">{{$loop->index+1}}</th>
                                    <td>{{str_limit($hour->arrangement->engagement->client->name,18)}}</td>
                                    <td>{{str_limit($hour->arrangement->engagement->name,18)}}</td>
                                    <td>{{$hour->billable_hours}}</td>
                                    <td>{{$hour->non_billable_hours}}</td>
                                    <td>{{$hour->report_date}}</td>
                                    <td>{{str_limit($hour->description,20)}}</td>
                                    <td><span class="label label-{{$hour->review_state==0?'success':'warning'}}">
                                                {{$hour->review_state==0?'APPROVED':'PENDING'}}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $hours->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection