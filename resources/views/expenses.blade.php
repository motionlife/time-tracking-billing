@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            {{--Begin of Modal--}}
            <div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-labelledby="expenseModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="expenseModalLabel">Expense Detail</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                        <form action="" id="hour-form">
                            <div class="modal-body">
                                <div class="panel-body">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client and Engagement:</span>
                                        <select id="client-engagement" class="selectpicker" data-width="auto" name="eid">
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i>&nbsp; Report Date</span>
                                        <input class="date-picker form-control" id="report-date"
                                               placeholder="mm/dd/yyyy"
                                               name="report_date" type="text" required/>
                                        <span class="input-group-addon"><i class="fa fa-check-square" aria-hidden="true"></i>&nbsp;Company Paid:</span>
                                        <select class="selectpicker" id="input-company-paid" name="company_paid" data-width="auto">
                                            <option value="yes">Yes</option>
                                            <option value="no" selected>No</option>
                                        </select>

                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-bed" aria-hidden="true"></i>&nbsp;Hotel:$</span>
                                        <input class="form-control" id="input-hotel" name="hotel" type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-plane" aria-hidden="true"></i>&nbsp;Flight:$</span>
                                        <input class="form-control" id="input-flight" name="flight"
                                               type="number" step="0.01" min="0" placeholder="numbers only">
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-coffee" aria-hidden="true"></i>&nbsp;Meal:$</span>
                                        <input class="form-control" id="input-meal" name="meal" type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;Office Supply:$</span>
                                        <input class="form-control" id="input-office-supply" name="office_supply"
                                               type="number" step="0.01" min="0" placeholder="numbers only">
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-car" aria-hidden="true"></i>&nbsp;Car Rental:$</span>
                                        <input class="form-control" id="input-car-rental" name="car_rental" type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-subway" aria-hidden="true"></i>&nbsp;Mileage Cost:$</span>
                                        <input class="form-control" id="input-mileage-cost" name="mileage_cost"
                                               type="number" step="0.01" min="0" placeholder="numbers only">
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-question" aria-hidden="true"></i>&nbsp;Other:$</span>
                                        <input class="form-control" id="input-other" name="other" type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-calculator" aria-hidden="true"></i>&nbsp;<strong>Total:$</strong></span>
                                        <input class="form-control" id="expense-total" name="total"
                                               type="number" step="0.01" min="0" placeholder="numbers only" disabled>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></i>&nbsp;Receipts:</span>
                                        <input class="form-control" id="input-file" name="receipt" type="file">
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
                            <h3 class="panel-title">Expenses History</h3>
                            <p class="panel-subtitle">{{$expenses->total()}} results</p>
                            <a class="btn btn-success update-pro" id="add-expense" href="javascript:void(0)" title="Report Your Expense"><i class="fa fa-plus" aria-hidden="true"></i> <span>Add New</span></a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel-body">
                            <div class="col-md-5">
                                <select class="selectpicker show-tick" data-width="auto" id="client-engagements"
                                        data-live-search="true">
                                    <option value="" data-icon="glyphicon-briefcase" selected>Client & Engagement
                                    </option>
                                    @foreach($clientIds as $cid=>$engagements)
                                        <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                                            @foreach($engagements as $eng)
                                                <option data-eid="{{$eng[0]}}" {{Request('eid')==$eng[0]?'selected':''}}>{{$eng[1]}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-inline" style="font-family:FontAwesome;">
                                <div class="form-group">
                                    <input class="date-picker form-control" id="start-date"
                                           placeholder="&#xf073; Start Day"
                                           value="{{Request('start')}}"
                                           type="text"/>
                                </div>
                                <span>-</span>
                                <div class="form-group">
                                    <input class="date-picker form-control" id="end-date" placeholder="&#xf073; End Day"
                                           value="{{Request('end')}}" type="text"/>
                                </div>
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
                            <th>Company Paid</th>
                            <th>Report Date</th>
                            <th>Total</th>
                            <th>Receipts</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Operate</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $offset = ($expenses->currentPage() - 1) * $expenses->perPage() + 1;?>
                        @foreach($expenses as $expense)
                            <?php $eng = $expense->arrangement->engagement ?>
                            <tr>
                                <th scope="row">{{$loop->index+$offset}}</th>
                                <td>{{str_limit($eng->name,22)}}</td>
                                <td>{{str_limit($eng->client->name,22)}}</td>
                                <td>{{$expense->company_paid?"Yes":"No"}}</td>
                                <td>{{$expense->report_date}}</td>
                                <td><strong>${{number_format($expense->total(),2)}}</strong></td>
                                <td>
                                    @foreach($expense->receipts as $receipt)
                                        <img src="{{$receipt->filename}}" alt="NTD">
                                    @endforeach
                                </td>
                                <td>{{str_limit($expense->description,37)}}</td>
                                <td><span class="label label-{!!$expense->getStatus()[1].'">'.$expense->getStatus()[0]!!}</span></td>
                                <td data-id="{{$expense->id}}"><a href="javascript:void(0)"><i
                                                class="fa fa-pencil-square-o"></i></a><a href="javascript:void(0)"><i
                                                class="fa fa-times"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pull-right pagination">
                    {{ $expenses->appends(Request::except('page'))->withPath('expense')->links() }}
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
                "timeOut": "2000"
            };
            $('#filter-button').on('click', function () {
                var eid = $('#client-engagements').find(":selected").attr('data-eid');
                window.location.href = '/expense?eid=' + (eid ? eid : '') +
                    '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
            });
            $('.date-picker').datepicker(
                {
                    format: 'mm/dd/yyyy',
                    todayHighlight: true,
                    autoclose: true
                }
            );
            $('#add-expense').on('click',function () {
                $('#expenseModal').modal('toggle');



            });
            $('td a:nth-child(1)').on('click', function () {
                tr = $(this).parent().parent();
                hid = $(this).parent().attr('data-id');
                $.get({
                    url: '/expense/' + hid + '/edit',
                    success: function (data) {
                        //update modal
                        $('#client-engagement').attr('disabled', true)
                            .empty().append('<option>' + data.ename + '</option>').selectpicker('refresh');
                        $('#position').attr('disabled', true)
                            .empty().append('<option>' + data.position + '</option>').selectpicker('refresh');
                        $('#task-id').find('option[data-tid="' + data.task_id + '"]').attr('selected', true).parent().selectpicker('refresh');
                        $('#report-date').datepicker('setDate', data.report_date);
                        $('#billable-hours').val(data.billable_hours);
                        $('#non-billable-hours').val(data.non_billable_hours);
                        $('#description').val(data.description);
                        $('#report-update').attr('disabled', data.review_state != "0");
                    },
                    dataType: 'json'
                });
                $('#expenseModal').modal('toggle');
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
                            url: "/expense/" + td.attr('data-id'),
                            data: {_token: "{{csrf_token()}}", _method: 'delete'},
                            success: function (data) {
                                if (data.message == 'succeed') {//remove item from the list
                                    td.parent().fadeOut(777, function(){ $(this).remove();});
                                    toastr.success('Success! Report has been deleted!');
                                } else {
                                    toastr.warning('Failed! Fail to delete the record!'+data.message);
                                }
                            },
                            error:function (data) {
                                toastr.warning('Failed! Fail to delete the record!'+data);
                                console.log(data);
                            },
                            dataType: 'json'
                        });
                    });
            });

            $('#hour-form').on('submit', function (e) {
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "/expense/" + hid,
                    data: {
                        //currently engagement and client are not allowed to be updated!!!
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
                            //no need to update engagement and client
//                            tr.find('td:nth-child(2)').html(feedback.record.ename);
//                            tr.find('td:nth-child(3)').html(feedback.record.cname);
                            tr.find('td:nth-child(4)').html(feedback.record.task);
                            tr.find('td:nth-child(5) strong').html(feedback.record.billable_hours);
                            tr.find('td:nth-child(6)').html(feedback.record.report_date);
                            tr.find('td:nth-child(7)').html(feedback.record.description);
                            tr.find('td:nth-child(8) span').removeClass().addClass('label label-' + feedback.record.status[1]).html(feedback.record.status[0]);
                            tr.find('td:nth-child(9)').attr('data-id', feedback.record.id);
                            var flash = tr;
                            flash.addClass('update-highlight');
                            setTimeout(function () {
                                flash.removeClass('update-highlight');
                            }, 2100);
                        } else {
                            toastr.error('Error! Updating failed, code: ' + feedback.code +
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
                        $('#expenseModal').modal('toggle');
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