@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-headline">
                    <div class="panel-heading">
                        <h3 class="panel-title">Time Reporting History</h3>
                        <p class="panel-subtitle">Panel Subtitle</p>
                    </div>
                    <div class="panel-body">
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