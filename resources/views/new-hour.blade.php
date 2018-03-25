@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row" style="margin-bottom: -2.4em;">
                <div class="col-md-3">
                    <h3 class="page-title">Working Time Report</h3>
                </div>
                <div class="col-md-9">
                    <div class="pull-right">
                        <label class="switch">
                            <input id="day-week" type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row daily-weekly-view dailyView" style="display: {{$interface=='weekly'?'none':'inline'}}">
                <div class="col-md-8">
                    <div class="panel panel-headline">
                        <div class="panel-heading">
                            <h3 class="panel-title">Consultant: {{Auth::user()->fullname()}}</h3>
                        </div>
                        <form method="POST" id="hour-form" action="/hour">
                            <div class="panel-body">
                                @component('components.hour-form',['clientIds'=>$clientIds,'withOldTasks'=>false])
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
                                            <a href="javascript:void(0);"><strong>{{number_format($hour->billable_hours+$hour->non_billable_hours,1)}}</strong></a>
                                        </div>
                                        <p>hours reported to
                                            <strong>{{$eng->name}}</strong> ({{$eng->client->name}}
                                            )<br>Billable:{{number_format($hour->billable_hours,1)}};
                                            Non-billable:{{number_format($hour->non_billable_hours,1)}}<span
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
            <div class="row daily-weekly-view weeklyView" style="display: {{$interface=='daily'?'none':'inline'}}">
                <div class="panel panel-headline">
                    <div class="input-group">
                        <span class="input-group-btn"><button class="btn btn-primary" type="button"
                                                              id="week-picker"><i
                                        class="fa fa-calendar-minus-o">&nbsp;Select Week</i></button></span>
                        <div class="form-control" id="week-info" style="border: dashed #5fdbff 0.1em;">
                            Week
                            <span class="badge bg-success">{{\Carbon\Carbon::now()->weekOfYear}}</span>&nbsp;<strong>{{\Carbon\Carbon::now()->startOfWeek()->format('m/d/Y').' - '.\Carbon\Carbon::now()->endOfWeek()->format('m/d/Y')}}</strong><i
                                    class="pull-right">Input <strong>Billable</strong> Hours</i>
                        </div>
                    </div>
                    <form id="matrix">
                        <div class="panel-body" id="hours-roll">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>[<span>Client</span>]Engagement<br><i>Task description</i></th>
                                    @for($i=0;$i<7;$i++)
                                        @php $date = \Carbon\Carbon::now()->startOfWeek(); @endphp
                                        <th>{{$date->addDay($i)->format('l')}}
                                            <br><span class="week-date"
                                                      data-date="{{$date->format('Y-m-d')}}">{{$date->format('M d')}}</span>
                                        </th>
                                    @endfor
                                    <th>DEL</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($defaultTasks as $ids)
                                    @php $id = explode('-',$ids); $eng=\newlifecfo\Models\Engagement::find($id[0]); @endphp
                                    <tr data-eid="{{$id[0]}}" data-pid="{{$id[1]}}" data-tid="{{$id[2]}}">
                                        <th scope="row"><span
                                                    class="label label-success">{{$eng->client->name}}</span><span>{{$eng->name}}</span><br><span>{{\newlifecfo\Models\Templates\Task::find($id[2])->description}}</span><a
                                                    href="javascript:void(0);" class="mark-fav-task"
                                                    title="Mark as your favorite task for automatic display."
                                                    data-state="{{$fav?'on':'off'}}"><i
                                                        class="fa fa-star{{$fav?'':'-o'}}" aria-hidden="true"></i></a>
                                        </th>
                                        @for($i=0;$i<7;$i++)
                                            <td><input class='form-control input-sm' type='number' min="0"
                                                       step="0.1" max="24"/>
                                                <a href="javascript:void(0);" title="Add description" ref="popover"
                                                   data-desc="">
                                                    <i class="fa fa-sticky-note-o" aria-hidden="true"></i></a></td>
                                        @endfor
                                        <td><a href="javascript:void(0)" class="deletable-row"><i
                                                        class="fa fa-times" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <a href="javascript:void(0)" id="show-row-modal" class="btn btn-info"><i class="fa fa-plus"
                                                                                                     aria-hidden="true">&nbsp;Add
                                    Row</i></a>
                            <i>&nbsp;</i>
                            <button type="submit" id="matrix-submit" class="btn btn-primary"
                                    data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing">Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="row-template" style="display:none;">
        <table>
            <tbody>
            <tr>
                <th scope="row"><span class="label label-success"></span><span></span><br><span></span><a
                            href="javascript:void(0);" class="mark-fav-task"
                            title="Mark as your favorite task for automatic display." data-state="off"><i
                                class="fa fa-star-o"
                                aria-hidden="true"></i></a>
                </th>
                @for($i=0;$i<7;$i++)
                    <td><input class='form-control input-sm' type='number' min="0" step="0.1" max="24"/>
                        <a href="javascript:void(0);" title="Add description" ref="popover"><i
                                    class="fa fa-sticky-note-o" aria-hidden="true"></i></a></td>
                @endfor
                <td><a href="javascript:void(0)" class="deletable-row"><i class="fa fa-times"
                                                                          aria-hidden="true"></i></a></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="engtaskModal" tabindex="-1" role="dialog" aria-labelledby="engtaskModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="validation-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="engtaskModalLabel">Select engagement and task</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group form-group-sm">
                            <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp;Engagement:</span>
                            @component('components.engagement_selector',['dom_id'=>"client-engagement-addrow",'clientIds'=>$clientIds])
                            @endcomponent
                            <span class="input-group-addon"><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;Position:</span>
                            <select class="selectpicker form-control form-control-sm" id="position-addrow" name="pid"
                                    data-width="100%"
                                    required></select>
                        </div>
                        <br>
                        <div class="input-group form-group-sm">
                            @component('components.task_selector',['dom_id'=>'task-id-addrow','withOldTasks'=>false])
                            @endcomponent
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="add-row-btn">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('my-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.3/moment.min.js"></script>
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
            $('#client-engagement,#client-engagement-addrow').on('change', function () {
                var select = $(this);
                $.ajax({
                    type: "get",
                    url: "/hour/create",
                    data: {eid: select.selectpicker('val'), fetch: 'position'},
                    success: function (data) {
                        var pos = $('#position,#position-addrow').empty();
                        $(data).each(function (i, arr) {
                            pos.append("<option value=" + arr.position.id + " data-br=" + arr.br + " data-fs=" + arr.fs + ">" + arr.position.name + "</option>");
                        });
                        pos.selectpicker('refresh');
                        $('#billable-hours').trigger("change");
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
                $('#income-estimate').val(bh + 'h  x  $' + (br * (1 - fs)).toFixed(2) + '/hr' + ' = $' + (bh * br * (1 - fs)).toFixed(2));
            });
            $('#hour-form').on('submit', function (e) {
                if (parseFloat($('#billable-hours').val()+$('#non-billable-hours').val()) > 0) {
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
                                $totalhours = parseFloat(feedback.data.billable_hours) + parseFloat(feedback.data.non_billable_hours);
                                $('<li><div class="pull-left avatar"><a href="javascript:void(0);"><strong>'
                                    + ($totalhours) + '</strong></a></div><p>hours reported to <strong>'
                                    + feedback.data.ename + '</strong>(' + feedback.data.cname + ')<br>Billable:' + feedback.data.billable_hours + '; Non-billable: '
                                    + feedback.data.non_billable_hours + '<span class="timestamp">'
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
                } else {
                    toastr.warning("Well, you should at least input some hours...");
                }
                e.preventDefault();
            });
            $('#report-date').datepicker({
                format: 'mm/dd/yyyy',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom'
            }).datepicker('setDate', new Date());
            $('#day-week').on('click', function () {
                $('.daily-weekly-view').slideToggle(300,function () {
                    $.get("?interface="+($('.weeklyView').is(":visible")?"weekly":"daily"));
                });
            });
            $('#hours-roll').slimScroll({
                height: '450px'
            });
            $('#week-picker').datepicker({
                todayHighlight: true,
                autoclose: true,
                calendarWeeks: true,
                weekStart: 1
            }).datepicker('setDate', new Date()).on('show', function () {
                $('.datepicker tr td.cw').parent().hover(function (e) {
                    $(this).css("background-color", e.type === "mouseenter" ? "#47cef7" : "transparent");
                });
            }).on('changeDate', function (e) {
                var weekinfo = $('#week-info');
                var md = moment(e.date);
                weekinfo.find('span').empty().text(md.week());
                var firstDate = md.day(1).format("MM/DD/YYYY");
                var lastDate = md.day(7).format("MM/DD/YYYY");
                weekinfo.find('strong').empty().text(firstDate + " - " + lastDate);
                var spans = $('#hours-roll').find('span.week-date');
                md.day(-1);
                for (var i = 0; i < 7; i++) {
                    var weekday = md.day(i+1);
                    spans.eq(i).empty().text(weekday.format("MMM DD"));
                    spans.eq(i).data('date', weekday.format('YYYY-MM-DD'));
                }
            });
            $('#show-row-modal').on('click', function () {
                $('#engtaskModal').modal('toggle');
            });
            $('#validation-form').on('submit', function (e) {
                var tr = $('#row-template').find('tr').clone().appendTo($('#hours-roll').find('tbody'));
                var engoption = $("#client-engagement-addrow").find("option:selected");
                var task = $("#task-id-addrow").find("option:selected");
                tr.data('eid', engoption.val());
                tr.data('pid', $('#position-addrow').val());
                tr.data('tid', task.val());
                tr.find('th span:first-child').text(engoption.parent().data('label'))
                    .next().text(engoption.text())
                    .next().next().text(task.data('task'));
                $('#engtaskModal').modal('toggle');
                $('#matrix-submit').attr('disabled', false);
                e.preventDefault();
            });
            $('#matrix').on('submit', function (e) {
                var data = [], json = [];
                data.push({name: '_token', value: "{{csrf_token()}}"}, {name: 'week', value: true});
                var spans = $('#hours-roll').find('span.week-date');
                $('#hours-roll').find('tbody tr').each(function (i, r) {
                    var eid = $(this).data('eid');
                    var pid = $(this).data('pid');
                    var tid = $(this).data('tid');
                    var inputs = $(r).find('td input');
                    var anchors = $(r).find('td a');
                    spans.each(function (j, d) {
                        var bh = inputs.eq(j).val();
                        var desc = anchors.eq(j).data('desc');
                        if (bh) {
                            var date = $(d).data('date');
                            json.push({
                                'eid': eid,
                                'pid': pid,
                                'tid': tid,
                                'date': date,
                                'bh': bh,
                                'desc': desc
                            });
                        }
                    });
                });
                data.push({name: 'json', value: JSON.stringify(json)});
                if (json.length) {
                    $.ajax({
                        type: "POST",
                        url: "/hour",
                        data: data,
                        dataType: 'json',
                        success: function (feedback) {
                            if (feedback.code == 7) {
                                toastr.success('<a href="/hour?state=0">Success! Report has been saved! Click to see detail.</a>');
                                var td = $('#hours-roll').find('tbody tr td');
                                td.find('input').val('');
                                td.find('a[ref="popover"]').data('desc', '').find('i').removeClass("fa-sticky-note").addClass("fa-sticky-note-o");
                            } else {
                                toastr.error('Error! Saving record failed, code: ' + feedback.code +
                                    ', message: ' + feedback.message);
                            }
                        },
                        error: function (feedback) {
                            toastr.error('Oh Noooooooo..' + feedback.message);
                        },
                        beforeSend: function () {
                            $("#matrix-submit").button('loading');
                        },
                        complete: function () {
                            $("#matrix-submit").button('reset');
                        }
                    });
                } else {
                    toastr.warning("Well, you should at least input some hours...");
                }
                e.preventDefault();
            });
            $(document).on('click', '.deletable-row', function () {
                $(this).parent().parent().fadeOut(300, function () {
                    $(this).remove();
                    if (!$('#hours-roll').find('tbody tr').length) $('#matrix-submit').attr('disabled', true);
                });
            }).on('click', '.mark-fav-task', function () {
                var star = $(this).find('i');
                var state = $(this).data('state');
                var anchor = $(this);
                var tr = anchor.parent().parent();
                $.ajax({
                    url: "?fav=task&eid=" + tr.data('eid') + "&pid=" + tr.data('pid') + "&tid=" + tr.data('tid'),
                    success: function (data) {
                        if (data.code == 7) {
                            if (state === 'off') {
                                anchor.data('state', 'on');
                                star.removeClass('fa fa-star-o').addClass('fa fa-star');
                            } else {
                                anchor.data('state', 'off');
                                star.removeClass('fa fa-star').addClass('fa fa-star-o');
                            }
                        }
                    }
                });
            });
            var focuseda;
            $('.main-content').popover({
                placement: 'bottom',
                container: '.main-content',
                selector: '[ref="popover"]',
                html: true,
                content: function () {
                    focuseda = $(this);
                    var content = focuseda.data("desc") === undefined ? '' : focuseda.data("desc");
                    return '<textarea class="notebook" id="notebook" rows="8" cols="70">' + content + '</textarea>';
                }
            }).on('click', function (e) {
                $('[ref="popover"]').each(function () {
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });
            $(document).on('change', '#notebook', function (e) {
                focuseda.data('desc', e.target.value);
                if (e.target.value) {
                    focuseda.find('i').removeClass("fa-sticky-note-o").addClass("fa-sticky-note");
                } else {
                    focuseda.find('i').removeClass("fa-sticky-note").addClass("fa-sticky-note-o");
                }
            });
        });
        @yield('task_selector')
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
    <style>
        #hours-roll thead span {
            color: #4bb3ff;
            font-weight: normal;
        }

        .deletable-row {
            color: red;
        }

        #hours-roll tbody tr th span:nth-child(2) {
            color: #287eff;
            font-weight: 600;
        }

        #week-info {
            font-size: 1.1em;
        }

        #hours-roll thead th:first-child i, tbody tr th span:last-child {
            font-weight: 700;
            font-size: small;
            color: rgba(91, 91, 93, 0.62);
        }

        #hours-roll tbody tr input[type=number] {
            width: 78%;
            display: inline-block;
            font-weight: bold;
        }

        #hours-roll tbody tr a {
            width: 18%;
            display: inline-block;
            margin-left: .1em;
        }

        a.mark-fav-task, a.mark-fav-task:hover {
            color: #ffc317;
        !important;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 26px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(31px);
            -ms-transform: translateX(31px);
            transform: translateX(31px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 30px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection
