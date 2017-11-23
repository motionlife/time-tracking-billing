@extends('layouts.app')
@section('content')
    <div class="main-content">
        {{--Begin of Modal--}}
        @if(isset($leader))
            <div class="modal fade" id="engagementModal" tabindex="-1" role="dialog"
                 aria-labelledby="engagementModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="engagementModalLabel">Setup A New Engagement</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                        <form action="" id="engagement-form">
                            {{csrf_field()}}
                            <div class="modal-body">
                                <div class="panel-body">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client:</span>
                                        <select id="client-select" class="selectpicker" data-width="auto" data-live-search="true"
                                                name="client_id">
                                            @foreach(\newlifecfo\Models\Client::all()->pluck('name','id') as $id=>$client)
                                                <option value="{{$id}}">{{$client}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-handshake-o"></i>&nbsp;Job Position:</span>
                                        <select class="selectpicker" id="position" name="pid"
                                                data-width="auto"></select>
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i>&nbsp; Report Date</span>
                                        <input class="date-picker form-control" id="start-date" name="start-date"
                                               placeholder="mm/dd/yyyy"
                                               name="report_date" type="text" required/>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tasks"></i>&nbsp;Task:</span>
                                        <select id="task-id" class="selectpicker show-sub-text" data-live-search="true"
                                                data-width="auto" name="task_id"
                                                title="Please select one of the tasks your did">
                                            @foreach(\newlifecfo\Models\Templates\Taskgroup::all() as $tgroup)
                                                <?php $gname = $tgroup->name?>
                                                @foreach($tgroup->tasks as $task)
                                                    <option value="{{$task->id}}"
                                                            data-content="{{$gname.' <strong>'.$task->description.'</strong>'}}"></option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-usd"></i>&nbsp;<strong>Billable Hours:</strong></span>
                                        <input class="form-control" id="billable-hours" name="billable_hours"
                                               type="number"
                                               placeholder="numbers only"
                                               step="0.1" min="0"
                                               max="24" required>

                                        <span class="input-group-addon"><i
                                                    class="fa fa-hourglass-start"></i>&nbsp;Non-billable Hours:</span>
                                        <input class="form-control" id="non-billable-hours" name="non_billable_hours"
                                               type="number" step="0.1" min="0"
                                               placeholder="numbers only">

                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button class="btn btn-primary" id="submit-modal" type="submit"
                                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing">Build
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        {{--END OF MODAL--}}
        <div class="container-fluid">
            <h3 class="page-title">{{isset($leader)?'Engagements I lead':'My Engagements'}}
                (total {{$engagements->count()}})</h3>
            <div class="up-border">
                <a href="javascript:void(0)" class="btn btn-success" id="build-engagement"
                   style="{{isset($leader)?'':'display: none'}}"><i class="fa fa-cubes">&nbsp; Build</i></a>
                <hr>
            </div>
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
                                            <td><i class="fa fa-circle-o"
                                                   aria-hidden="true"></i>{{$engagement->state()}}</td>
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
            $('.date-picker').datepicker(
                {
                    format: 'mm/dd/yyyy',
                    todayHighlight: true,
                    autoclose: true
                }
            );
            $('.slim-scroll').slimScroll({
                height: '150px'
            });
            $('#build-engagement').on('click', function () {
                $('#engagementModal').modal('toggle');
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

        .up-border {
            margin: -0.8em 0 -0.9em 0;
        }

        td > i {
            color: #19ff38;
            font-size: 0.7em;
            margin-right: 0.5em;
        }
    </style>
@endsection
