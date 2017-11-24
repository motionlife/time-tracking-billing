@extends('layouts.app')
@section('content')
    @php $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT); $manage=isset($leader); @endphp
    <div class="main-content">
        {{--Begin of Modal--}}
        @if($manage)
            <div class="modal fade" id="engagementModal" tabindex="-1" role="dialog"
                 aria-labelledby="engagementModalLabel" data-backdrop="static" data-keyboard="false"
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
                                        <span class="input-group-addon"><i class="fa fa-briefcase"
                                                                           aria-hidden="true"></i>&nbsp;Name:</span>
                                        <input type="text" class="form-control" id="engagement-name" name="name"
                                               placeholder="input a name" required>
                                        <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client:</span>
                                        <select id="client-select" class="selectpicker" data-width="auto"
                                                data-live-search="true"
                                                name="client_id" title="select the client" required>
                                            @foreach(\newlifecfo\Models\Client::all()->pluck('name','id') as $id=>$client)
                                                <option value="{{$id}}"
                                                        data-content="<strong>{{$client}}</strong>"></option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i>&nbsp; Leader:</span>
                                        <input type="text" class="form-control" value="{{$leader->fullname()}}"
                                               disabled>
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i>&nbsp; Start Date</span>
                                        <input class="date-picker form-control" id="start-date" name="start_date"
                                               placeholder="mm/dd/yyyy" type="text" required/>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-handshake-o"
                                                                           aria-hidden="true"></i>&nbsp; Buziness Dev:</span>
                                        <input type="text" class="form-control" id="buz_dev_person" value="New Life CFO"
                                               disabled>
                                        <span class="input-group-addon"><i class="fa fa-pie-chart"></i>&nbsp;Dev Share:</span>
                                        <input class="form-control" id="buz_dev_share" name="buz_dev_share"
                                               type="number"
                                               placeholder="pct."
                                               step="0.1" min="0"
                                               max="24" required>
                                        <span class="input-group-addon">%</span>

                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-hourglass-half"
                                                                           aria-hidden="true"></i>&nbsp;Client Billed Type:</span>
                                        <select id="cycle-select" class="selectpicker" data-width="25%"
                                                name="paying_cycle" required>
                                            @for($i=0;$i<4;$i++)
                                                <option value="{{$i}}">{{\newlifecfo\Models\Engagement::billedType($i)}}</option>
                                            @endfor
                                        </select>
                                        <span class="input-group-addon"><i
                                                    class="fa fa-money"></i>&nbsp;Billing Amount:<strong>$</strong></span>
                                        <input class="form-control" id="billing_amount" name="cycle_billing"
                                               type="number" step="0.01" min="0"
                                               placeholder="N/A" disabled>

                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button class="btn btn-primary" id="submit-modal" type="submit"
                                        data-loading-text="<i class='fa fa-spinner fa-spin'></i>Processing">Build
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        {{--END OF MODAL--}}
        <div class="container-fluid">
            <h3 class="page-title">{{$manage?'Engagements I lead':'Engagements I\'m in'}}
                (total {{$engagements->count()}})</h3>
            <div class="up-border">
                @if($manage)
                    <a href="javascript:void(0)" class="btn btn-success" id="build-engagement"><i class="fa fa-cubes">&nbsp;
                            Build</i></a>
                @endif
                <div class="form-inline {{ $manage?'pull-right':''}}" style="font-family:FontAwesome;">
                    <div class="form-group"">
                        <select class="selectpicker show-tick" data-width="fit" id="client-filter"
                                data-live-search="true">
                            <option value="" data-icon="glyphicon glyphicon-leaf" selected>Clients</option>
                            @foreach(\newlifecfo\Models\Client::all()->pluck('name','id') as $id=>$client)
                                <option value="{{$id}}" data-content="<strong>{{$client}}</strong>" {{Request('cid')==$id?'selected':''}}></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="date-picker form-control" id="start-date-filter"
                               placeholder="&#xf073; Start Day"
                               value="{{Request('start')}}"
                               type="text"/>
                    </div>
                    <div class="form-group">
                        <a href="javascript:void(0)" type="button" class="btn btn-info" id="filter-button">Filter</a>
                    </div>
                </div>
                <hr>
            </div>
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
            $('#start-date').datepicker('setDate', new Date());
            $('#filter-button').on('click', function () {
                var cid = $('#client-filter').selectpicker('val');
                window.location.href = '/engagement{{$manage?'/create':''}}?cid=' + (cid ? cid : '') +
                    '&start=' + $('#start-date-filter').val();
            });
            $('#client-select').on('change', function () {
                $.get({
                    url: '/engagement/create?fetch=business&cid=' + $(this).selectpicker('val'),
                    success: function (dev) {
                        $('#buz_dev_person').empty().val(dev);
                    }
                })
            });
            $('#cycle-select').on('change', function () {
                if ($(this).selectpicker('val') != 0) {
                    $('#billing_amount').attr('disabled', false).attr('placeholder', 'per cycle');
                } else {
                    $('#billing_amount').attr('placeholder', 'N/A').attr('disabled', true).val('');
                }
            });
            $('.slim-scroll').slimScroll({
                height: '150px'
            });
            $('#build-engagement').on('click', function () {
                //modal initialization

                $('#engagementModal').modal('toggle');
            });
        });

        function initModal() {
            $('#billing_amount').val('').attr('disabled', true);
        }
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
