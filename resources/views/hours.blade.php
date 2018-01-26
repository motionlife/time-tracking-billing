@extends('layouts.app')
@section('content')
    @php $mcMode = $admin||Request::get('reporter')=='team'; @endphp
    <div class="main-content">
        <div class="container-fluid">
            <div class="modal fade" id="hourModal" tabindex="-1" role="dialog" aria-labelledby="hourModalLabel"
                 data-backdrop="static" data-keyboard="false"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="hourModalLabel">Consultant:
                                <span id="consultant-name" style="color: #4bb3ff;"></span>
                                <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </a>
                            </h3>
                        </div>
                        <form action="" id="hour-form">
                            <div class="modal-body">
                                <div class="panel-body">
                                    @component('components.hour-form',['admin'=>$admin,'clientIds'=>null])
                                    @endcomponent
                                </div>
                            </div>
                            <div class="modal-footer" style="margin-top: -0.7em">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button class="btn btn-primary" id="report-update" type="submit"
                                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing"><i
                                            class="{{$admin?'fa fa-paper-plane':''}}" aria-hidden="true"></i>&nbsp;Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="panel panel-headline">
                <div class="row">
                    @if($confirm)
                        @component('components.confirm',['confirm'=>$confirm,'reports'=>$hours])
                        @endcomponent
                    @else
                        <div class="panel-heading col-md-2">
                            <h3 class="panel-title">{{$admin?'Hour Pool':'Time History'}}</h3>
                            <p class="panel-subtitle">{{$hours->total()}} results</p>
                        </div>
                        <div class="panel-body col-md-10">
                            @component('components.filter',['clientIds'=>$clientIds,'admin'=>$admin,'target'=>'hour'])
                            @endcomponent
                        </div>
                    @endif
                </div>
                <div class="panel-body no-padding">
                    <table class="table table-striped table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Engagement<a href="{{url()->current().'?'.http_build_query(Request::except('eid'))}}">&nbsp;<i
                                            class="fa fa-refresh" aria-hidden="true"></i></a></th>
                            @if($confirm)
                                <th>Paid</th>
                                <th>Billing</th>
                            @else
                                <th>Task</th>
                            @endif
                            <th>Billable Hours</th>
                            <th>Report Date</th>
                            <th>{!!$mcMode?'Consultant<a href="'.url()->current().'?'.http_build_query(Request::except('conid')).'">&nbsp;<i class="fa fa-refresh" aria-hidden="true"></i></a>':'Description'!!}</th>
                            <th>Status</th>
                            <th>Operate</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $offset = ($hours->currentPage() - 1) * $hours->perPage() + 1;?>
                        @foreach($hours as $hour)
                            @php
                                $arr = $hour->arrangement;
                                $eng = $arr->engagement;
                                $cname =$hour->consultant->fullname();
                            @endphp
                            <tr>
                                <th scope="row">{{$loop->index+$offset}}</th>
                                <td>{{str_limit($hour->client->name,19)}}</td>
                                <td>
                                    <a href="{{str_replace_first('/','',route('hour.index',array_add(Request::except('eid','page'),'eid',$eng->id),false))}}">{{str_limit($eng->name,19)}}</a>
                                </td>
                                @if($confirm)
                                    <td>${{number_format($hour->earned(),2)}}</td>
                                    <td>${{number_format($hour->billClient(),2)}}</td>
                                @else
                                    <td>{{str_limit($hour->task->getDesc(),23)}}</td>
                                @endif
                                <td>{{number_format($hour->billable_hours,2)}}</td>
                                <td>{{$hour->report_date}}</td>
                                <td>
                                    @if($mcMode)
                                        <a href="{{url()->current().'?'.http_build_query(Request::except('conid','page')).'&conid='.$hour->consultant_id}}">{{str_limit($cname,25)}}</a>
                                    @else
                                        {{str_limit($hour->description,29)}}
                                    @endif
                                </td>
                                <td><span class="label label-{{$hour->getStatus()[1]}}">{{$hour->getStatus()[0]}}</span>
                                </td>
                                <td data-id="{{$hour->id}}"><a href="javascript:void(0)"><i
                                                class="fa fa-pencil-square-o"></i></a><a href="javascript:void(0)"
                                                                                         data-del="{{($admin||!$hour->isApproved())&&!$confirm?1:0}}"><i
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
            var tr;
            var hid;
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

            $('#billable-hours').on('change', function () {
                var income = $('#income-estimate');
                var br = income.attr('data-br');
                var fs = income.attr('data-fs');
                var bh = $(this).val();
                income.val(bh + 'h  x  $' + br + '/hr  x  ' + (1 - fs) * 100 + '% = $' + (bh * br * (1 - fs)).toFixed(2));
            });

            $('tr td:last-child a:nth-child(1)').on('click', function () {
                tr = $(this).parent().parent();
                hid = $(this).parent().attr('data-id');
                $.get({
                    url: '/hour/' + hid + '/edit',
                    success: function (data) {
                        $('#income-estimate').attr({"data-br": data.rate, "data-fs": data.share});
                        $('#client-engagement').attr('disabled', true)
                            .empty().append('<option selected>' + data.client + '/' + data.ename + '</option>').selectpicker('refresh');
                        $('#position').attr('disabled', true)
                            .empty().append('<option>' + data.position + '</option>').selectpicker('refresh');
                        $('#task-id').selectpicker('val', data.task_id);
                        $('#report-date').datepicker('setDate', data.report_date);
                        $('#billable-hours').val(data.billable_hours).trigger("change");
                        $('#non-billable-hours').val(data.non_billable_hours);
                        $('#description').val(data.description);
                        $('#report-update').attr('disabled', data.review_state !== "0");
                        $('#consultant-name').text(data.cname);
                        @if($admin)
                        $('#report-update').attr('disabled', false);
                        $("input[name=review_state][value=" + data.review_state + "]").prop('checked', true);
                        if (data.review_state === "0") $("input[name=review_state]").prop('checked', false);
                        $('#hour-feedback').val(data.feedback);
                        @else
                        if (data.review_state !== "0" && data.feedback !== null)
                            $('#feedback-info').addClass('alert alert-success').text('Message: ' + data.feedback);
                        else $('#feedback-info').removeClass('alert alert-success').text('');
                        @endif
                    },
                    dataType: 'json',
                    complete: function () {
                        @if($confirm)
                        $('#report-update').attr('disabled', true);
                        @endif
                    }
                });
                $('#hourModal').modal('toggle');
            });
            $('tr td:last-child a:nth-child(2)').on('click', function () {
                if ($(this).data('del') == '1') {
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
                                    if (data.message == 'succeed') {
                                        td.parent().fadeOut(1000, function () {
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
                } else {
                    toastr.warning('Action not allowed here or admin priority is needed.')
                }
            });

            $('#hour-form').on('submit', function (e) {
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "/hour/" + hid,
                    data: {
                        _token: token,
                        _method: 'put',
                        report_date: $('#report-date').val(),
                        task_id: $('#task-id').selectpicker('val'),
                        billable_hours: $('#billable-hours').val(),
                        non_billable_hours: $('#non-billable-hours').val(),
                        description: $('#description').val(),
                        @if($admin)
                        review_state: $("input[name=review_state]:checked").val(),
                        feedback: $('#hour-feedback').val()
                        @endif
                    },
                    dataType: 'json',
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            toastr.success('Success! Report has been updated!');
                            tr.find('td:nth-child(4)').html(feedback.record.task);
                            tr.find('td:nth-child(5)').html(feedback.record.billable_hours);
                            tr.find('td:nth-child(6)').html(feedback.record.report_date);
                            @if(!$mcMode) tr.find('td:nth-child(7)').html(feedback.record.description);
                            @endif
                            tr.find('td:nth-child(8) span').removeClass().addClass('label label-' + feedback.record.status[1]).html(feedback.record.status[0]);
                            tr.find('td:nth-child(9)').attr('data-id', feedback.record.id);
                            var flash = tr;
                            flash.addClass('update-highlight');
                            setTimeout(function () {
                                flash.removeClass('update-highlight');
                            }, 2100);
                        } else {
                            toastr.error('Error! Updating failed, code: ' + feedback.code +
                                ', message: ' + feedback.message);
                        }
                    },
                    error: function (feedback) {
                        toastr.error('Oh NOooooooo...' + feedback.message);
                    },
                    beforeSend: function () {
                        $("#report-update").button('loading');
                    },
                    complete: function () {
                        $("#report-update").button('reset');
                        $('#hourModal').modal('toggle');
                    }
                });
                e.preventDefault();
            });
        });
    </script>
    <style>
        tr td:last-child a:nth-child(2) {
            color: red;
            margin-left: 1.5em;
        }

        .panel tr td:nth-child({{$confirm?6:5}}) {
            text-indent: 1em;
            font-weight: 600;
        }
    </style>
    @yield('confirm_module')
@endsection
