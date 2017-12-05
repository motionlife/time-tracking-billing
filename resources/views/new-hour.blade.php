@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-headline">
                    <div class="panel-heading">
                        <h3 class="panel-title">Working Time Report</h3>
                        <p class="panel-subtitle">Consultant: {{Auth::user()->fullName()}}</p>
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
                <!-- TIMELINE -->
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
                                        <strong>{{$eng->name}}</strong> ({{$eng->client->name}})<span class="timestamp">{{\Carbon\Carbon::parse($hour->created_at)->diffForHumans()}}
                                            <a href="javascript:deleteTodaysReport({{$hour->id}});"><i
                                                        class="fa fa-times pull-right"></i></a></span>

                                    </p>

                                </li>
                            @endforeach
                        </ul>
                        <a type="button" href="/hour" class="btn btn-primary btn-bottom center-block">See All</a>
                    </div>
                </div>
                <!-- END TIMELINE -->
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
                    //fetch the corresponding position for him and add option to position option
                    type: "get",
                    url: "/hour/create",
                    data: {eid: select.selectpicker('val'), fetch: 'position'},
                    success: function (data) {
                        var pos = $('#position').empty();
                        $(data).each(function (i, arr) {
                            pos.append("<option value=" + arr.position.id + " data-br=" + arr.br + " data-fs=" + arr.fs + ">" + arr.position.name + "</option>");
                        });
                        pos.selectpicker('refresh');
                        //also store the billing rate and firm share
                    }
                });
            });
            $('#position').on('change',function(){$('#billable-hours').trigger("change");});
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
                        //notify the user
                        if (feedback.code == 7) {
                            toastr.success('Success! Report has been saved!');
                            //clear some data for the user
                            $('#billable-hours').val('');
                            $('#non-billable-hours').val('');
                            //update today's board
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
                        //notify the user
                        toastr.error('Oh Noooooooo..' + feedback.message);
                    },
                    beforeSend: function () {
                        //spinner begin to spin
                        $("#report-button").button('loading');
                    },
                    complete: function () {
                        //button spinner stop
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
                            if (data.message == 'succeed') {//remove item from the list
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
