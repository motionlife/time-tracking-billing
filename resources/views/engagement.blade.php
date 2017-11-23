@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title" id="panel-scrolling-demo">My Engagements</h3>
            @php $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT); @endphp
            @foreach($engagements as $engagement)
                @if($loop->index%2==0)
                    <div class="row">
                        @endif

                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Name: <strong>{{$engagement->name}}</strong></h3>
                                    <p class="panel-subtitle">Client: <strong>{{$engagement->client->name}}</strong></p>
                                    <table class="table table-striped table-bordered table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Leader</th>
                                            <th>Started</th>
                                            <th>Buz Dev Share</th>
                                            <th>Billed Type</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{$engagement->leader->fullname()}}</td>
                                            <td>{{$engagement->start_date}}</td>
                                            <td>{{$formatter->format($engagement->buz_dev_share)}}</td>
                                            <td>{{$engagement->clientBilledType()}}</td>
                                            <td><i class="fa fa-circle-o" aria-hidden="true"></i>{{$engagement->state()}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-body slim-scroll">
                                    <div id="demo-line-chart" class="">
                                        @php $hourly = $engagement->clientBilledType() == 'Hourly'; @endphp
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Consultant</th>
                                                <th>Position</th>
                                                <th>{{$hourly?'Billing Rate':'Pay Rate'}}</th>
                                                <th>Firm Share</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($engagement->arrangements as $arrangement)
                                                <tr>
                                                    <th scope="row">{{$loop->index+1}}</th>
                                                    <td>{{$arrangement->consultant->fullname()}}</td>
                                                    <td> {{$arrangement->position->name}}</td>
                                                    <td>${{$arrangement->billing_rate}}</td>
                                                    <td>{{$hourly? $formatter->format($arrangement->firm_share):'-'}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($loop->index%2==1||$loop->last)
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
@section('my-js')
    <script>
        $(function () {
            $('.slim-scroll').slimScroll({
                height: '150px'
            });
        });
    </script>
@endsection

@section('special-css')
    <style>
        .table td, .table th {
            text-align: center;
        }
        .panel-subtitle strong {
            color: #27b2ff;
        }
        td >i {
            color: #19ff38;
            font-size: 0.7em;
            margin-right: 0.5em;
        }
    </style>
@endsection
