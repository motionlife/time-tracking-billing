@extends('layouts.app')
@section('popup-container')
    <div id="billing-day-container"></div>
@endsection
@section('content')
    @php $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT); $manage=isset($leader); @endphp
    <div class="main-content">
        @if($manage||$admin)
            <div class="modal fade" id="engagementModal" tabindex="-1" role="dialog"
                 aria-labelledby="engagementModalLabel" data-backdrop="static" data-keyboard="false"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="engagementModalLabel"><span>Setup A New Engagement</span>
                                <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </a>
                            </h3>
                        </div>
                        <form action="" id="engagement-form">
                            <div class="modal-body">
                                <div class="panel-body">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client:</span>
                                        <select id="client-select" class="selectpicker" data-width="auto"
                                                data-live-search="true"
                                                name="client_id" title="select the client" required>
                                            @foreach(\newlifecfo\Models\Client::all()->pluck('name','id') as $id=>$client)
                                                <option value="{{$id}}"
                                                        data-content="<strong>{{$client}}</strong>"></option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><i class="fa fa-briefcase"
                                                                           aria-hidden="true"></i>&nbsp;Name:</span>
                                        <input type="text" list="engagement-names" class="form-control flexdatalist"
                                               id="engagement-name" name="name"
                                               placeholder="input a name" data-selection-required='true'
                                               data-min-length='0' data-search-by-word='true' required>
                                        <datalist id="engagement-names">
                                            <option value="CFO Services">
                                            <option value="Controller Services">
                                            <option value="Business Intelligence">
                                        </datalist>
                                        <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i>&nbsp; Leader:</span>
                                        <select class="selectpicker" name="leader_id" id="leader_id" data-width="auto"
                                                disabled>
                                            @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                                                <option value="{{$consultant->id}}" {{($manage&&$consultant->id==$leader->id)?'selected':''}}>{{$consultant->fullname()}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i>&nbsp; Start Date</span>
                                        <input class="date-picker form-control" id="start-date" name="start_date"
                                               placeholder="mm/dd/yyyy" type="text" required/>
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
                                        <select id="cycle-select" class="selectpicker" data-width="auto"
                                                name="paying_cycle" required>
                                            <option value="0">Hourly</option>
                                            <option value="1">Monthly Retainer</option>
                                            <option value="2">Fixed Fee Project</option>
                                        </select>
                                        <span class="input-group-addon"><i
                                                    class="fa fa-money"></i>&nbsp;Billing Amount:<strong>$</strong></span>
                                        <input class="form-control" id="billing_amount" name="cycle_billing"
                                               type="number" step="0.1" min="0" placeholder="N/A">
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar-check-o"></i>&nbsp; Billing Day</span>
                                        <input class="form-control" id="billing-day" name="billing_day"
                                               placeholder="dd" type="number" min="1" max="31" step="1" required/>
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
                                            <th>Billing Rate</th>
                                            <th>Pay Rate</th>
                                            <th>Firm Share%</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="members-table">
                                        <tr>
                                            <td>
                                                <select class="selectpicker cid" data-width="150px"
                                                        data-dropup-auto="false"
                                                        data-live-search="true"
                                                        required disabled>
                                                    @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                                                        <option value="{{$consultant->id}}">{{$consultant->fullname()}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="selectpicker pid" data-width="200px"
                                                        data-dropup-auto="false" required disabled>
                                                    @foreach(\newlifecfo\Models\Templates\Position::all() as $position)
                                                        <option value="{{$position->id}}">{{$position->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step=0.01 min=0 class="form-control b-rate"></td>
                                            <td><input type="number" step=0.01 min=0 class="form-control p-rate"></td>
                                            <td><input type="number" step=0.01 min=0 max=100
                                                       class="form-control f-share"></td>
                                            <td><a href="javascript:void(0);"><i class="fa fa-minus-circle"
                                                                                 aria-hidden="true"></i></a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($admin)
                                <div class="row"
                                     style="width:95%;border-style: dotted;color:#33c0ff;padding: .2em .2em .2em .2em;margin-left: 1.4em">
                                    <div class="col-md-4">
                                        <label class="fancy-radio">
                                            <input name="status" value="0" type="radio">
                                            <span><i></i><p class="label label-warning">Pending</p></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fancy-radio">
                                            <input name="status" value="1" type="radio">
                                            <span><i></i><p class="label label-success">Active</p></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fancy-radio">
                                            <input name="status" value="2" type="radio">
                                            <span><i></i><p class="label label-default">Closed</p></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <h3 class="page-title"
                        style="margin: auto;">{{$manage?'Engagements I lead':($admin?'Engagement Pool':'Engagements I\'m in')}}
                        (total {{$engagements->count()}})</h3>
                </div>
                <div class="col-md-8">
                    <div class="form-inline pull-right" style="font-family:FontAwesome;" id="filter-selection">
                        @if($manage)
                            <a href="javascript:void(0)" class="btn btn-success" id="build-engagement"><i
                                        class="fa fa-cubes">&nbsp;
                                    Build</i></a>
                            <i>&nbsp;</i>
                        @endif
                        <a href="#" type="button" class="btn btn-default reset-btn" title="Reset all condition"><i
                                    class="fa fa-refresh" aria-hidden="true"></i></a>
                        <select class="selectpicker show-tick" data-width="fit" id="client-filter"
                                data-live-search="true" title="&#xf06c; All Clients">
                            @foreach($clients as $client)
                                <option value="{{$client['id']}}"
                                        data-content="<strong>{{$client['name']}}</strong>" {{Request('cid')==$client['id']?'selected':''}}></option>
                            @endforeach
                        </select>
                        @if(!$manage)
                            <select class="selectpicker show-tick" data-width="fit" id="leader-filter"
                                    data-live-search="true" title="&#xf2be; Leader">
                                @foreach($leaders as $leader)
                                    <option value="{{$leader->id}}" {{Request('lid')==$leader->id?'selected':''}}>{{$leader->fullname()}}</option>
                                @endforeach
                            </select>
                        @endif
                        <select class="selectpicker form-control" data-width="fit"
                                id="status-select"
                                data-live-search="true" title="&#xf024; Status">
                            <option value="0" {{Request('status')=="0"?'selected':''}}>Pending</option>
                            <option value="1" {{Request('status')=="1"?'selected':''}}>Active</option>
                            <option value="2" {{Request('status')=="2"?'selected':''}}>Closed</option>
                        </select>
                        <input class="date-picker form-control" size=10 id="start-date-filter"
                               placeholder="&#xf073; Start after"
                               value="{{Request('start')}}"
                               type="text"/>
                        <a href="javascript:void(0)" type="button" class="btn btn-info" id="filter-button">Filter</a>
                    </div>
                </div>
            </div>
            <hr>
            @foreach($engagements as $engagement)
                @if($loop->index%2==0)
                    <div class="row">
                        @endif
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading engagement-table">
                                    <h3 class="panel-title">Name: <strong>{{$engagement->name}}</strong>
                                        @if($manage||$admin)
                                            <div class="pull-right">
                                                <a href="javascript:void(0)" class="eng-edit"
                                                   data-id="{{$engagement->id}}"><i
                                                            class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                <span>&nbsp;|&nbsp;</span>
                                                <a href="javascript:void(0)" class="eng-delete" data-del="{{!$engagement->isPending()&&$manage?'0':'1'}}"
                                                   data-id="{{$engagement->id}}"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            </div>
                                        @endif
                                    </h3>
                                    <div class="panel-subtitle">Client: <strong>{{$engagement->client->name}}</strong>
                                        <span class="label label-info pull-right">Total Members: <strong>{{$engagement->arrangements->count()}}</strong></span>
                                    </div>
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
                                            <td>{{str_limit($engagement->clientBilledType(),11)}}</td>
                                            <td><i class="fa fa-flag {{$engagement->state()}}"
                                                   aria-hidden="true"></i>{{$engagement->state()}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if(!$admin)
                                    <div class="panel-body slim-scroll arrangement-table">
                                        @php $hourly = $engagement->clientBilledType() == 'Hourly'; @endphp
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>Consultant</th>
                                                <th>Position</th>
                                                <th>{{$hourly?'Billing Rate':'Pay Rate'}}</th>
                                                <th>Firm Share</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($engagement->arrangements as $arrangement)
                                                <tr>
                                                    <td>{{$arrangement->consultant->fullname()}}</td>
                                                    <td> {{$arrangement->position->name}}</td>
                                                    <td>
                                                        @can('view',$arrangement)
                                                            ${{$hourly?$arrangement->billing_rate:$arrangement->pay_rate}}
                                                        @endcan
                                                    </td>
                                                    <td>
                                                        @can('view',$arrangement)
                                                            {{$hourly? $formatter->format($arrangement->firm_share):'-'}}
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
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
            $('#billing-day').datepicker({
                container: '#billing-day-container',
                format: 'dd',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom'
            });
            $('#start-date').datepicker('setDate', new Date());
            $('#filter-button').on('click', function () {
                var query = '?cid=' + $('#client-filter').selectpicker('val')
                    + '&start=' + $('#start-date-filter').val()
                    + '&status=' + $('#status-select').selectpicker('val');
                @if(!$manage) query += '&lid=' + $('#leader-filter').selectpicker('val');
                @endif
                    window.location.href = query;
            });
            $('#filter-selection').find('a.reset-btn').on('click', function () {
                $('#filter-selection').find('select.selectpicker').selectpicker('val', '');
                $('#filter-selection').find('.date-picker').val("").datepicker("update");
                $('#filter-button').trigger('click');
            });
            @if($admin)
            $('input[type=radio][name=status]').change(function () {
                $('#submit-modal').attr('disabled', false);
            });
            @endif
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
                    $('.b-rate').val('').attr('disabled', true);
                    $('.p-rate').val('').attr('disabled', false);
                } else {
                    $('#billing_amount').attr({'placeholder': 'N/A', 'disabled': true}).val('');
                    $('.b-rate').val('').attr('disabled', false);
                    $('.p-rate').val('').attr('disabled', true);
                }
                $('#billing-day').attr('disabled', this.value != 1).val('');
                $('.f-share').attr('disabled', this.value != 0).val('');
            });
            $('.slim-scroll').slimScroll({
                height: '130px'
            });
            $('#member-roll').slimScroll({
                height: '220px'
            });
            $('#build-engagement').on('click', function () {
                update = false;
                initModal(false);
                $('#engagementModal').modal('toggle');
            });
            $('.eng-delete').on('click', function () {
                if($(this).data('del')==0){
                    toastr.warning('Can\'t delete non-pending engagement here.');
                    return;
                }
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
                                if (data.message == 'succeed') {
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
                $.get({
                    url: '/engagement/' + $(this).attr('data-id') + '/edit?admin={{$admin}}',
                    success: function (data) {
                        $('#cycle-select').selectpicker('val', data.paying_cycle).trigger('change');
                        $('#engagement-name').val(data.name);
                        $('#client-select').selectpicker('val', data.client_id);
                        $('#leader_id').selectpicker('val', data.leader_id);
                        $('#start-date').val(data.start_date);
                        $('#buz_dev_share').val(parseFloat(data.buz_dev_share * 100).toFixed(2));
                        $('#billing_amount').val(parseFloat(data.cycle_billing).toFixed(2));
                        $('#billing-day').datepicker('setDate', new Date('2022-12-' + data.billing_day + ' 00:00'));
                        $('#submit-modal').attr('disabled', @if($admin) false
                        @else data.status != 0  @endif );
                        @if($admin)
                        $("input[name=status][value=" + data.status + "]").prop('checked', true);
                                @endif
                        var table = $('#members-table');
                        var tr = table.find('tr').first();
                        $.each(data.arrangements, function (i, o) {
                            tr.find('.cid').selectpicker('val', o.consultant_id);
                            tr.find('.pid').selectpicker('val', o.position_id);
                            tr.find('.b-rate').val(parseFloat(o.billing_rate).toFixed(2));
                            tr.find('.p-rate').val(parseFloat(o.pay_rate).toFixed(2));
                            tr.find('.f-share').val(parseFloat(o.firm_share * 100).toFixed(2));
                            if (data.arrangements[i + 1]) {
                                tr = tr.clone().appendTo(table);
                                tr.find('a').addClass("deletable-row");
                                tr.find('.bootstrap-select').replaceWith(function () {
                                    return $('select', this);
                                });
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
                            toastr.success(update ? feedback.message : 'Engagement has been created!');
                            setTimeout(location.reload.bind(location), 1000);
                        } else if (feedback.code == 5) {
                            toastr.warning(feedback.message);
                        }
                        else {
                            toastr.error('Error! Saving failed, code: ' + feedback.code +
                                ', message: ' + feedback.message);
                        }
                    },
                    error: function (feedback) {
                        toastr.error('Oh NOooooooo...' + feedback.message);
                    },
                    beforeSend: function (jqXHR, settings) {
                        $("#submit-modal").button('loading');
                    },
                    complete: function () {
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
                tr.find('.cid').attr('disabled', false).selectpicker('val', '');
                tr.find('.pid').attr('disabled', false).selectpicker('val', '');
                tr.find('input').val('');
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
                            toastr.success('Consultant will be removed after updating!');
                        });
                } else {
                    tr.fadeOut(300, function () {
                        $(this).remove();
                    });
                }
            });
        });

        function initModal(update) {
            var tb = $("#members-table");
            tb.find("tr:not(:first-child)").remove();
            tb.find("tr .selectpicker").selectpicker('refresh');
            if (!update) {
                $('#cycle-select').selectpicker('val', 0).trigger('change');
                $('#engagement-name').val('');
                $('#submit-modal').text('Build').attr('disabled', false);
                $('#engagementModalLabel').find('span').text('Setup A New Engagement');
                tb.find('select').first().selectpicker('val', $('#leader_id').val());
                tb.find('select').last().selectpicker('val', 8);
            } else {
                $('#submit-modal').html('Update');
                $('#engagementModalLabel').find('span').text('Update Engagement')
            }
        }

        function pushArrangements(form) {
            $('#members-table').find('tr').each(function () {
                form.push({name: 'consultant_ids[]', value: $(this).find('.cid').selectpicker('val')},
                    {name: 'position_ids[]', value: $(this).find('.pid').selectpicker('val')},
                    {name: 'billing_rates[]', value: $(this).find('.b-rate').val()},
                    {name: 'pay_rates[]', value: $(this).find('.p-rate').val()},
                    {name: 'firm_shares[]', value: $(this).find('.f-share').val()}
                );
            });
        }

    </script>
@endsection
@section('special-css')
    <style>
        .arrangement-table {
            margin-top: -3.8%;
        }

        .engagement-table {
            margin-bottom: -1.2em;
        }

        .table td, .table th {
            text-align: center;
        }

        .deletable-row {
            color: red;
        }

        .panel-subtitle > strong {
            color: #27b2ff;
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

        .fancy-radio .label {
            font-size: small;
        }

        #members-table tr td input[type='number'] {
            text-align: center;
        }

        #billing-day-container div.datepicker-days thead {
            display: none;
        }
    </style>
@endsection
