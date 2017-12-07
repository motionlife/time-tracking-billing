@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row" style="margin-bottom: -2.4em;">
                <div class="col-md-3">
                    <h3 class="page-title">Working Time Report</h3>
                </div>
                <div class="col-md-9">
                    <a href="javascript:void(0)" class="btn btn-default"
                       id="day-week">Daily&nbsp;<i class="fa fa-refresh" aria-hidden="true">&nbsp;Weekly</i></a>
                </div>
            </div>
            <hr>
            <div class="row daily-weekly-view" style="display: none">
                <div class="col-md-8">
                    <div class="panel panel-headline">
                        <div class="panel-heading">
                            <h3 class="panel-title">Consultant: {{Auth::user()->fullname()}}</h3>
                        </div>
                        <form method="POST" id="hour-form" action="/hour">
                            <div class="panel-body">
                                @component('components.hour-form',['clientIds'=>$clientIds])
                                @endcomponent
                            </div>
                            <div class="panel-footer">
                                <button class="btn btn-primary" id="report-button" type="submit"
                                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing">Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-scrolling">
                        <div class="panel-heading">
                            <h3 class="panel-title">Today's Reports</h3>
                            <p class="panel-subtitle">{{$hours->count()?$hours->count()." reports":"No report today"}}</p>
                            <div class="right">
                                <button type="button" class="btn-toggle-collapse"><i class="lnr lnr-chevron-up"></i>
                                </button>
                                <button type="button" class="btn-remove"><i class="lnr lnr-cross"></i></button>
                            </div>
                        </div>
                        <div class="panel-body">
                            <ul class="list-unstyled activity-list" id="today-board">
                                @foreach($hours as $hour)
                                    <li>
                                        <?php $eng = $hour->arrangement->engagement ?>
                                        <div class="pull-left avatar">
                                            <a href="javascript:void(0);"><strong>{{number_format($hour->billable_hours,1)}}</strong></a>
                                        </div>
                                        <p> billable hours reported for the work of
                                            <strong>{{$eng->name}}</strong> ({{$eng->client->name}})<span
                                                    class="timestamp">{{\Carbon\Carbon::parse($hour->created_at)->diffForHumans()}}
                                                <a href="javascript:deleteTodaysReport({{$hour->id}});"><i
                                                            class="fa fa-times pull-right"></i></a></span>

                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                            <a type="button" href="/hour" class="btn btn-primary btn-bottom center-block">See All</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row daily-weekly-view" >
                <div class="panel panel-headline">
                    <div class="panel-heading form-inline">
                        <label title="Week">
                            Select Week: <input class="form-control" type="week">
                        </label>
                    </div>
                    <div class="panel-body" id="hours-roll">
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th>Engagement / Task</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wes</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                                <th>Sun</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">#</th>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>
                                    <td><input class='form-control input-sm' type='text' size="4"/></td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <a href="javascript:void(0)" class="btn btn-info"><i class="fa fa-plus" aria-hidden="true">&nbsp;Add
                                Row</i></a>
                        <i>&nbsp;</i>
                        <a href="javascript:void(0)" class="btn btn-primary">Submit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('my-js')
    <script>
        $(function () {
            toastr.options = {
                "positionClass": "toast-bottom-full-width",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "4000",
                "extendedTimeOut": "900"
            };
            $('#client-engagement').on('change', function () {
                var select = $(this);
                $.ajax({
                    type: "get",
                    url: "/hour/create",
                    data: {eid: select.selectpicker('val'), fetch: 'position'},
                    success: function (data) {
                        var pos = $('#position').empty();
                        $(data).each(function (i, arr) {
                            pos.append("<option value=" + arr.position.id + " data-br=" + arr.br + " data-fs=" + arr.fs + ">" + arr.position.name + "</option>");
                        });
                        pos.selectpicker('refresh');
                    }
                });
            });
            $('#position').on('change', function () {
                $('#billable-hours').trigger("change");
            });
            $('#billable-hours').on('change', function () {
                var opt = $('#position').find(':selected');
                var br = opt.attr('data-br');
                var fs = opt.attr('data-fs');
                var bh = $(this).val();
                $('#income-estimate').val(bh + 'h  x  $' + br + '/hr  x  ' + (1 - fs) * 100 + '% = $' + bh * br * (1 - fs));
            });
            $('#hour-form').on('submit', function (e) {
                var eid = $('#client-engagement').selectpicker('val');
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "/hour",
                    data: {
                        _token: token,
                        eid: eid ? eid : '',
                        pid: $('#position').selectpicker('val'),
                        report_date: $('#report-date').val(),
                        task_id: $('#task-id').selectpicker('val'),
                        billable_hours: $('#billable-hours').val(),
                        non_billable_hours: $('#non-billable-hours').val(),
                        description: $('#description').val()
                    },
                    dataType: 'json',
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            toastr.success('Success! Report has been saved!');
                            $('#billable-hours').val('');
                            $('#non-billable-hours').val('');
                            $('<li><div class="pull-left avatar"><a href="javascript:void(0);"><strong>'
                                + feedback.data.billable_hours + '</strong></a></div><p>billable hours reported for the work of <strong>'
                                + feedback.data.ename + '</strong>(' + feedback.data.cname + ')<span class="timestamp">'
                                + feedback.data.created_at + '<a href="javascript:deleteTodaysReport('
                                + feedback.data.hid + ');"><i class="fa fa-times pull-right"></i></a></span></p></li>')
                                .prependTo('#today-board').hide().fadeIn(1500);
                        } else {
                            toastr.error('Error! Saving record failed, code: ' + feedback.code +
                                ', message: ' + feedback.message);
                        }
                    },
                    error: function (feedback) {
                        toastr.error('Oh Noooooooo..' + feedback.message);
                    },
                    beforeSend: function () {
                        $("#report-button").button('loading');
                    },
                    complete: function () {
                        $("#report-button").button('reset');
                    }
                });
                e.preventDefault();
            });
            $('#report-date').datepicker({
                format: 'mm/dd/yyyy',
                todayHighlight: true,
                autoclose: true
            }).datepicker('setDate', new Date());

            $('#day-week').on('click', function () {
                $('.daily-weekly-view').slideToggle();
            });
            $('#hours-roll').slimScroll({
                height: '220px'
            });

        });

        function deleteTodaysReport(hid) {
            var li = $('a[href*="deleteTodaysReport(' + hid + ')"]').parent().parent().parent();
            swal({
                    title: "Are you sure?",
                    text: "This record shall be deleted, please make sure!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function () {
                    $.post({
                        url: "/hour/" + hid,
                        data: {_token: "{{csrf_token()}}", _method: 'delete'},
                        success: function (data) {
                            if (data.message == 'succeed') {
                                li.fadeOut(1000, function () {
                                    $(this).remove();
                                });
                                swal("Deleted!", "The record has been deleted.", "success");
                            } else {
                                toastr.warning('Failed! Fail to delete the record!' + data.message);
                            }
                        },
                        dataType: 'json'
                    });
                });
        }
    </script>
@endsection

@section('special-css')
@endsection
