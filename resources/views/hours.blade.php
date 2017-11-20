@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            {{--Begin of Modal--}}
            <div class="modal fade" id="hourModal" tabindex="-1" role="dialog" aria-labelledby="hourModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="hourModalLabel">Reported Record Detail</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                        <form action="" id="hour-form">
                            <div class="modal-body">
                                <div class="panel-body">
                                    <div class="input-group">
                            <span class="input-group-addon"><i
                                        class="fa fa-users">&nbsp; Client and Engagement:</i></span>
                                        <select id="client-engagement" class="selectpicker show-tick" data-width="auto"
                                                data-live-search="true" name="eid"
                                                title="Select from the engagements your're currently in" required>
                                            @foreach($clientIds as $cid=>$engagements)
                                                <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                                                    @foreach($engagements as $eng)
                                                        <option data-eid="{{$eng[0]}}">{{$eng[1]}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-handshake-o">&nbsp;Job Position:</i></span>
                                        <select class="selectpicker" id="position" name="pid" data-width="auto"
                                                required></select>
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i>&nbsp; Report Date</span>
                                        <input class="date-picker form-control" id="report-date"
                                               placeholder="mm/dd/yyyy"
                                               name="report_date" type="text" required/>

                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tasks">&nbsp;Task:</i></span>
                                        <select id="task-id" class="selectpicker show-sub-text" data-live-search="true"
                                                data-width="auto" name="task_id"
                                                title="Please select one of the tasks your did">
                                            @foreach(\newlifecfo\Models\Templates\Taskgroup::all() as $tgroup)
                                                <?php $gname = $tgroup->name?>
                                                @foreach($tgroup->tasks as $task)
                                                    <option data-tid="{{$task->id}}"
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
                                                    class="fa fa-hourglass-start">&nbsp;Non-billable Hours:</i></span>
                                        <input class="form-control" id="non-billable-hours" name="non_billable_hours"
                                               type="number" step="0.1" min="0"
                                               placeholder="numbers only">

                                    </div>
                                    <br>
                                    <textarea id="description" class="form-control" name="description"
                                              placeholder="description"
                                              rows="5"></textarea>
                                    <br>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button class="btn btn-primary" id="report-update" type="submit"
                                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing">Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{--END OF MODAL--}}
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
                                <select class="selectpicker show-tick" data-width="auto" id="client-engagements"
                                        data-live-search="true">
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
                                <td class="e-name">{{str_limit($hour->arrangement->engagement->name,19)}}</td>
                                <td>{{str_limit($hour->arrangement->engagement->client->name,19)}}</td>
                                <td>{{str_limit($hour->task->getDesc(),23)}}</td>
                                <td><strong>{{number_format($hour->billable_hours,1)}}</strong></td>
                                <td>{{$hour->report_date}}</td>
                                <td>{{str_limit($hour->description,29)}}</td>
                                <td><span class="label label-{!!$hour->getStatus()[1].'">'.$hour->getStatus()[0]!!}</span></td>
                                <td data-id="{{$hour->id}}"><a href="javascript:void(0)"><i
                                                class="fa fa-pencil-square-o"></i></a><a href="javascript:void(0)"><i
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
            var hid;
            toastr.options = {
                "positionClass": "toast-top-center",
                "timeOut": "2000"
            };
            $('#filter-button').on('click', function () {
                var eid = $('#client-engagements').find(":selected").attr('data-eid');
                window.location.href = '/hour?eid=' + (eid ? eid : '') +
                    '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
            });
            $('.date-picker').datepicker(
                {
                    format: 'mm/dd/yyyy',
                    todayHighlight: true,
                    autoclose: true
                }
            );

            $('td a:nth-child(1)').on('click', function () {
                hid = $(this).parent().attr('data-id');
                $.get({
                    url: '/hour/' + hid,
                    success: function (data) {
                        //update modal
                        $('#client-engagement').find('option[data-eid="' + data.eid + '"]').attr('selected', true);
                        $('#client-engagement').selectpicker('refresh');
                        $('#position').empty().append('<option>'+data.position+'</option>');
                        $('#position').selectpicker('refresh');
                        $('#task-id').find('option[data-tid="' + data.task_id + '"]').attr('selected', true);
                        $('#task-id').selectpicker('refresh');
                        $('#report-date').datepicker('setDate', data.report_date);
                        $('#billable-hours').val(data.billable_hours);
                        $('#non-billable-hours').val(data.non_billable_hours);
                        $('#description').val(data.description);
                        //if(data.review_state=="0") $('#report-date').attr('disabled',true);
                    },
                    dataType: 'json'
                });
                $('#hourModal').modal('toggle');
            });
            $('td a:nth-child(2)').on('click', function () {
                var td = $(this).parent();
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this record after delete it!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!"
                    },
                    function () {
                        $.post({
                            url: "/hour/" + td.attr('data-id'),
                            data: {_token: "{{csrf_token()}}", _method: 'delete'},
                            success: function (data) {
                                if (data.message == 'succeed') {//remove item from the list
                                    td.parent().remove();
                                    toastr.success('Success! Report has been deleted!');
                                } else {
                                    toastr.warning('Failed! Fail to delete the record!');
                                }
                            },
                            dataType: 'json'
                        });
                    });
            });

            $('#client-engagement').on('change', function () {
                $.ajax({
                    //fetch the corresponding position for him and add option to position option
                    type: "get",
                    url: "/hour/create",
                    data: {eid: $('#client-engagement').find(":selected").attr('data-eid'), fetch: 'position'},
                    success: function (data) {
                        $('#position').empty();
                        $(data).each(function (i, e) {
                            $('#position').append("<option data-pid=" + e.id + ">" + e.name + "</option>");
                        });
                        $('#position').selectpicker('refresh');
                    }
                });
            });

            $('#hour-form').on('submit', function (e) {
                var eid = $('#client-engagement').find(":selected").attr('data-eid');
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "/hour/" + hid,
                    data: {
                        _token: token,
                        _method: 'put',
                        report_date: $('#report-date').val(),
                        task_id: $('#task-id').find(":selected").attr('data-tid'),
                        billable_hours: $('#billable-hours').val(),
                        non_billable_hours: $('#non-billable-hours').val(),
                        description: $('#description').val()
                    },
                    dataType: 'json',
                    success: function (feedback) {
                        //notify the user
                        if (feedback.code == 7) {
                            toastr.success('Success! Report has been updated!');
                            $('#hourModal').modal('toggle');
                            //update some data for the user
                            //$('#billable-hours').val('new data');
                            //$('#non-billable-hours').val('new data');
                        } else {
                            toastr.error('Error! An error happened during this operation, code: ' + feedback.code +
                                ', message: ' + feedback.message)
                        }
                    },
                    error: function (feedback) {
                        //notify the user
                        toastr.error('Oh NOooooooo...' + feedback.message);
                    },
                    beforeSend: function () {
                        //spinner begin to spin
                        $("#report-update").button('loading');
                    },
                    complete: function () {
                        //button spinner stop
                        $("#report-update").button('reset');
                    }
                });
                e.preventDefault();
            });
        });
    </script>
    <style>
        td a:nth-child(2) {
            color: red;
            margin-left: 1.5em;
        }
    </style>
@endsection()
