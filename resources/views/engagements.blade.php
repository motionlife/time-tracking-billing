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
                                        <select class="selectpicker" name="leader_id" id="leader_id" disabled>
                                            <option value="{{$leader->id}}" selected>{{$leader->fullname()}}</option>
                                        </select>
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
                                               max="100" required>
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
                                               type="number" step="0.1" min="0" placeholder="N/A">

                                    </div>
                                    <br>
                                </div>
                                <a id="add-team-member" href="javascript:void(0)" class="label label-info"><i
                                            class="fa fa-user-plus" aria-hidden="true"></i>Add members
                                </a>
                                <div class="panel-footer" id="member-roll">
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Consultant</th>
                                            <th>Position</th>
                                            <th id="bill-pay-head">Billing Rate</th>
                                            <th>Firm Share%</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="members-table">
                                        <tr>
                                            <td>
                                                <select class="selectpicker cid" data-width="120px"
                                                        data-live-search="true"
                                                        disabled required>
                                                    @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                                                        <option value="{{$consultant->id}}" {{$leader->id==$consultant->id?"selected":""}}>{{$consultant->fullname()}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="selectpicker pid" data-width="140px" required>
                                                    @foreach(\newlifecfo\Models\Templates\Position::all() as $position)
                                                        <option value="{{$position->id}}" {{$position->name=="CFO_Lead"?"selected":""}}>{{$position->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step=0.01 min=0 class="form-control b-rate"></td>
                                            <td><input type="number" step=0.01 min=0 max=100
                                                       class="form-control f-share"></td>
                                            <td>
                                                <a href="javascript:void(0);"><i class="fa fa-minus-circle"
                                                                                 aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
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
                    <div class="form-group"
                    ">
                    <select class="selectpicker show-tick" data-width="fit" id="client-filter"
                            data-live-search="true">
                        <option value="" data-icon="glyphicon glyphicon-leaf" selected>All Clients</option>
                        @foreach($cids as $cid)
                            <option value="{{$cid}}"
                                    data-content="<strong>{{\newlifecfo\Models\Client::find($cid)->name}}</strong>" {{Request('cid')==$cid?'selected':''}}></option>
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
                                <h3 class="panel-title">Name: <strong>{{$engagement->name}}</strong>
                                    @if($manage)
                                        <div class="pull-right">
                                            <a href="javascript:void(0)" class="eng-edit" data-id="{{$engagement->id}}"><i
                                                        class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                            <span>&nbsp;|&nbsp;</span>
                                            <a href="javascript:void(0)" class="eng-delete"
                                               data-id="{{$engagement->id}}"><i
                                                        class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </div>
                                    @endif
                                </h3>
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
                                        <td><i class="fa fa-flag {{$engagement->state()}}"
                                               aria-hidden="true"></i>{{$engagement->state()}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel-body slim-scroll member-table">
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
                                                <td>
                                                    @can('view',$arrangement)
                                                        ${{$arrangement->billing_rate}}
                                                    @endcan
                                                </td>
                                                <td>  @can('view',$arrangement)
                                                        {{$hourly? $formatter->format($arrangement->firm_share):'-'}}
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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
            var update;
            var eid;
            $.fn.selectpicker.Constructor.DEFAULTS.dropupAuto = false;
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
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
            $('#cycle-select').on('changed.bs.select', function (e) {
                if (this.value != 0) {
                    $('#billing_amount').attr('disabled', false).attr('placeholder', 'per cycle');
                    $('#bill-pay-head').html('Pay Rate');
                } else {
                    $('#billing_amount').attr({'placeholder': 'N/A', 'disabled': true}).val('');
                    $('#bill-pay-head').html('Billing Rate');
                }
            });
            $('.slim-scroll').slimScroll({
                height: '130px'
            });
            $('#member-roll').slimScroll({
                height: '220px'
            });
            $('#build-engagement').on('click', function () {
                update = false;
                //modal initialization
                initModal(false);
                $('#submit-modal').text('Build');
                $('#engagementModal').modal('toggle');
            });

            $('.eng-delete').on('click', function () {
                var id = $(this).attr('data-id');
                var anchor = $(this);
                swal({
                        title: "Are you sure?",
                        text: "Consultants under this engagement will not be able to report hours to it after delete it!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!"
                    },
                    function () {
                        $.post({
                            url: "/engagement/" + id,
                            data: {_token: "{{csrf_token()}}", _method: 'delete'},
                            success: function (data) {
                                if (data.message == 'succeed') {//remove item from the list
                                    anchor.parent().parent().parent().parent().fadeOut(700, function () {
                                        $(this).remove();
                                    });
                                    toastr.success('Success! Report has been deleted!');
                                } else {
                                    toastr.warning('Failed! Fail to delete the record!' + data.message);
                                }
                            },
                            dataType: 'json'
                        });
                    });
            });
            $('.eng-edit').on('click', function () {
                initModal(true);
                $('#submit-modal').html('Update');
                $.get({
                    url: '/engagement/' + $(this).attr('data-id') + '/edit',
                    success: function (data) {
                        //update modal
                        $('#engagement-name').val(data.name);
                        $('#client-select').selectpicker('val', data.client_id);
                        $('#leader_id').selectpicker('val', data.leader_id);
                        $('#start-date').val(data.start_date);
                        $('#buz_dev_share').val(data.buz_dev_share * 100);
                        $('#cycle-select').selectpicker('val', data.paying_cycle);
                        $('#billing_amount').val(data.cycle_billing);
                        if (data.paying_cycle !== "0") {
                            $('#bill-pay-head').html('Pay Rate');
                            $('#billing_amount').attr('disabled', false);
                        }
                        else {
                            $('#bill-pay-head').html('Billing Rate');
                            $('#billing_amount').attr({'placeholder': 'N/A', 'disabled': true}).val('');
                        }
                        var table = $('#members-table');
                        var tr = table.find('tr').first();
                        $.each(data.arrangements, function (i, o) {
                            tr.find('.cid').selectpicker('val', o.consultant_id);
                            tr.find('.pid').selectpicker('val', o.position_id);
                            tr.find('.b-rate').val(o.billing_rate);
                            tr.find('.f-share').val(o.firm_share * 100);
                            if (data.arrangements[i + 1]) {
                                tr = tr.clone().appendTo(table);
                                tr.find('a').addClass("deletable-row");
                                tr.find('.bootstrap-select').replaceWith(function () {
                                    return $('select', this);
                                });
                                tr.find('select').first().attr('disabled', false).selectpicker('val', '');
                                tr.find('select').last().selectpicker('val', '');
                            }
                        });
                    },
                    dataType: 'json'
                });
                $('#engagementModal').modal('toggle');
                eid = $(this).attr('data-id');
                update = true;
            });

            $('#engagement-form').on('submit', function (e) {
                e.preventDefault();
                formdata = $(this).serializeArray();
                formdata.push({name: '_token', value: "{{csrf_token()}}"}, {
                    name: 'leader_id', value: $('#leader_id').selectpicker('val')
                });
                if (update) formdata.push({name: '_method', value: 'PUT'});
                pushArrangements(formdata);
                $.post({
                    url: update ? "/engagement/" + eid : "/engagement",
                    data: formdata,
                    dataType: 'json',
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            toastr.success('Success! Engagement has been ' + update ? 'updated!' : 'created!');
                            setTimeout(location.reload.bind(location), 1000);
                        } else {
                            toastr.error('Error! Saving failed, code: ' + feedback.code +
                                ', message: ' + feedback.message);
                        }
                    },
                    error: function (feedback) {
                        toastr.error('Oh NOooooooo...' + feedback.message);
                    },
                    beforeSend: function (jqXHR, settings) {
                        //spinner begin to spin
                        $("#submit-modal").button('loading');
                    },
                    complete: function () {
                        //button spinner stop
                        $("#submit-modal").button('reset');
                        $('#engagementModal').modal('toggle');
                    }
                });
                return false;
            });

            $('#add-team-member').on('click', function () {
                var table = $('#members-table');
                var tr = table.find('tr').first().clone().appendTo(table);
                tr.find('a').addClass("deletable-row");
                tr.find('.bootstrap-select').replaceWith(function () {
                    return $('select', this);
                });
                tr.find('select').first().attr('disabled', false).selectpicker('val', '');
                tr.find('select').last().selectpicker('val', '');

            });
            $(document).on('click', '.deletable-row', function () {
                var tr = $(this).parent().parent();
                if (update) {
                    swal({
                            title: "Are you sure?",
                            text: "Once the consultant has been removed he/she can no longer report hours to it any more!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, remove!"
                        },
                        function () {
                            tr.fadeOut(300, function () {
                                $(this).remove();
                            });
                            toastr.success('Consultant been removed!');
                        });
                } else {
                    tr.fadeOut(300, function () {
                        $(this).remove();
                    });
                }
            });
        });

        function initModal(update) {
            //$('#billing_amount').val('').attr('disabled', true);
            var tb = $("#members-table");
            tb.find("tr:not(:first-child)").remove();
            tb.find("tr.selectpicker").selectpicker('refresh');
            if (!update) {
                $('#cycle-select').selectpicker('val', 0);
                $('#bill-pay-head').html('Billing Rate');
                $('#billing_amount').val('').attr('disabled', true);
            }
        }

        function pushArrangements(form) {
            $('#members-table').find('tr').each(function () {
                form.push({name: 'consultant_ids[]', value: $(this).find('.cid').selectpicker('val')},
                    {name: 'position_ids[]', value: $(this).find('.pid').selectpicker('val')},
                    {name: 'billing_rates[]', value: $(this).find('.b-rate').val()},
                    {name: 'firm_shares[]', value: $(this).find('.f-share').val()}
                );
            });
        }

    </script>
@endsection

@section('special-css')
    <style>
        .member-table{
            margin-top: -4.0%;
        }
        .table td, .table th {
            text-align: center;
        }

        .deletable-row {
            color: red;
        }

        .panel-subtitle strong {
            color: #27b2ff;
        }

        .up-border {
            margin: -0.8em 0 -0.9em 0;
        }

        td > i {
            font-size: 0.7em;
            margin-right: 0.5em;
        }

        td > i.Pending {
            color: red;
        }

        td > i.Active {
            color: #19ff38;
        }

        td > i.Closed {
            color: Grey;
        }
    </style>
@endsection
