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
                        <form action="" id="expense-form">
                            <div class="modal-body">
                                <div class="panel-body">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client and Engagement:</span>
                                        <select id="client-engagement" class="selectpicker" data-width="auto"
                                                name="engagement"
                                                data-live-search="true"
                                                title="The engagements your expense related to" required>
                                        </select>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i>&nbsp; Report Date</span>
                                        <input class="date-picker form-control" id="input-report-date"
                                               placeholder="mm/dd/yyyy"
                                               name="report_date" type="text" required/>
                                        <span class="input-group-addon"><i class="fa fa-calendar-check-o"
                                                                           aria-hidden="true"></i>&nbsp;Company Paid:</span>
                                        <select class="selectpicker" id="input-company-paid" name="company_paid"
                                                data-width="auto">
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>

                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-bed" aria-hidden="true"></i>&nbsp;Hotel:$</span>
                                        <input class="form-control input-numbers" id="input-hotel" name="hotel"
                                               type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-plane" aria-hidden="true"></i>&nbsp;Flight:$</span>
                                        <input class="form-control input-numbers" id="input-flight" name="flight"
                                               type="number" step="0.01" min="0" placeholder="numbers only">
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-coffee" aria-hidden="true"></i>&nbsp;Meal:$</span>
                                        <input class="form-control input-numbers" id="input-meal" name="meal"
                                               type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-paperclip"
                                                                           aria-hidden="true"></i>&nbsp;Office Supply:$</span>
                                        <input class="form-control input-numbers" id="input-office-supply"
                                               name="office_supply"
                                               type="number" step="0.01" min="0" placeholder="numbers only">
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-car" aria-hidden="true"></i>&nbsp;Car Rental:$</span>
                                        <input class="form-control input-numbers" id="input-car-rental"
                                               name="car_rental" type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-taxi" aria-hidden="true"></i>&nbsp;Mileage Cost:$</span>
                                        <input class="form-control input-numbers" id="input-mileage-cost"
                                               name="mileage_cost"
                                               type="number" step="0.01" min="0" placeholder="numbers only">
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-question"
                                                                           aria-hidden="true"></i>&nbsp;Other:$</span>
                                        <input class="form-control input-numbers" id="input-other" name="other"
                                               type="number" placeholder="numbers only"
                                               step="0.01" min="0">
                                        <span class="input-group-addon"><i class="fa fa-calculator"
                                                                           aria-hidden="true"></i>&nbsp;<strong>Total:$</strong></span>
                                        <input class="form-control" id="expense-total" name="total" type="number"
                                               step="0.01" disabled>
                                    </div>
                                    <br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-file-text"
                                                                           aria-hidden="true"></i>&nbsp;Receipts:</span>
                                        <input class="form-control" id="input-receipts" name="receipts" type="file"
                                               multiple>
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
                                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{--END OF MODAL--}}

            <div class="panel panel-headline">
                <div class="row">
                    <div class="panel-heading col-md-3">
                        <h3 class="panel-title">Expenses History</h3>
                        <p class="panel-subtitle">{{$expenses->total()}} results</p>
                        <a class="btn btn-success update-pro" id="add-expense" href="javascript:void(0)"
                           title="Report Your Expense"><i class="fa fa-plus" aria-hidden="true"></i>
                            <span>Add New</span></a>
                    </div>

                    <div class="panel-body col-md-9">
                        <div class="form-inline pull-right" style="font-family:FontAwesome;">
                            <div class="form-group">
                                <select class="selectpicker show-tick" data-width="auto" id="client-engagements"
                                        data-live-search="true">
                                    <option value="" data-icon="glyphicon-briefcase" selected>Client & Engagement
                                    </option>
                                    @foreach($clientIds as $cid=>$engagements)
                                        <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                                            @foreach($engagements as $eng)
                                                <option value="{{$eng[0]}}" {{Request('eid')==$eng[0]?'selected':''}}>{{$eng[1]}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

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
                            <div class="form-group">
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
                        <tbody id="main-table">
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
                                <td><a href=" javascript:editExpense({{$expense->id}})"><i
                                            class="fa fa-pencil-square-o"></i></a><a
                                            href="javascript:deleteExpense({{$expense->id}})"><i
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
        var update;//boolean indicate whether user intend to update or report new
        var expid;
        var tr;
        $(function () {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "2000"
            };
            $('.input-numbers').on('change', function () {
               updateTotal();
            });
            $('#filter-button').on('click', function () {
                var eid = $('#client-engagements').selectpicker('val');
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
            //user want add a new record
            $('#add-expense').on('click', function () {
                $('#expenseModal').modal('toggle');
                $('#client-engagement').html($('#client-engagements').html()).selectpicker('refresh');
                $('form .input-group input').val('');
                $('#input-report-date').datepicker('setDate', new Date());
                $('#report-update').html('Report').attr('disabled', false);
                update = false;
            });

            //update or store => server side update or create Eloquent model
            $('#expense-form').on('submit', function (e) {
                e.preventDefault();
                if (!$('#client-engagement').selectpicker('val')) {
                    toastr.warning('Please select an engagement your expense related to');
                    return;
                } else if ($('#expense-total').val() == 0) {
                    toastr.warning('Expense total at least greater than 0');
                    return;
                }
                if (update) tr = $('a[href*="editExpense(' + expid + ')"]').parent().parent();
                $.ajax({
                    type: "POST",
                    url: update ? "/expense/" + expid : "/expense",
                    dataType: 'json',
                    data: {
                        _token: "{{csrf_token()}}",
                        _method: update ? 'put' : 'post',
                        eid: $('#client-engagement').selectpicker('val'),
                        report_date: $('#input-report-date').val() || 0,
                        company_paid: $('#input-company-paid').val() || 0,
                        flight: $('#input-flight').val() || 0,
                        meal: $('#input-meal').val() || 0,
                        office_supply: $('#input-office-supply').val() || 0,
                        car_rental: $('#input-car-rental').val() || 0,
                        mileage_cost: $('#input-mileage-cost').val() || 0,
                        other: $('#input-other').val() || 0,
                        receipts: $('#input-receipts').val(),
                        description: $('#description').val()
                    },
                    success: function (feedback) {
                        //notify the user
                        if (feedback.code == 7) {
                            if (update) {
                                toastr.success('Success! Expense has been updated!');
                                tr.find('td:nth-child(4)').html(feedback.record.company_paid);
                                tr.find('td:nth-child(5)').html(feedback.record.report_date);
                                tr.find('td:nth-child(6) strong').html('$'+feedback.record.total);
                                tr.find('td:nth-child(7)').html(feedback.record.receipts);
                                tr.find('td:nth-child(8)').html(feedback.record.description);
                                tr.find('td:nth-child(9) span').removeClass().addClass('label label-' + feedback.record.status[1]).html(feedback.record.status[0]);
                                tr.addClass('update-highlight');
                                setTimeout(function () {
                                    tr.removeClass('update-highlight');
                                }, 2100);
                            } else {
                                //prepend it at the top of the list
                                toastr.success('Success! Expense has been created!');
                                $('<tr><th scope="row">*</th><td>' + feedback.data.ename + '</td><td>' + feedback.data.cname + '</td><td>' + feedback.data.company_paid + '</td><td>' + feedback.data.report_date + '</td><td><strong>' + feedback.data.total + '</strong></td><td>' + feedback.data.receipts + '</td><td>' + feedback.data.description + '</td><td><span class="label label-' + feedback.data.status[1] + '">' + feedback.data.status[0] + '</span></td><td><a href="javascript:editExpense(' + feedback.data.expid + ')"><i class="fa fa-pencil-square-o"></i></a><a href="javascript:deleteExpense(' + feedback.data.expid + ')"><i class="fa fa-times"></i></a></td></tr>')
                                    .prependTo('#main-table').hide().fadeIn(1500);
                            }
                        } else {
                            toastr.error('Error! Saving failed, code: ' + feedback.code +
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
            });
        });

        //fill the for form to let user EDIT the record
        function editExpense(id) {
            $('#report-update').html('Update');
            update = true;
            expid = id;
            $.get({
                url: '/expense/' + id + '/edit',
                success: function (data) {
                    //set the form in modal
                    $('#client-engagement').html('<option selected>' + data.ename + '</option>').selectpicker('refresh');
                    $('#input-report-date').datepicker('setDate', data.report_date);
                    $('#input-company-paid').val(data.company_paid);
                    $('#input-flight').val(data.flight);
                    $('#input-meal').val(data.meal);
                    $('#input-office-supply').val(data.office_supply);
                    $('#input-car-rental').val(data.car_rental);
                    $('#input-mileage-cost').val(data.mileage_cost);
                    $('#input-other').val(data.other);
                    $('#input-receipts').val(data.receipts);
                    $('#description').val(data.description);
                    $('#expense-total').val(data.total);
                    $('#report-update').attr('disabled', data.review_state != "0");
                },
                dataType: 'json'
            });
            $('#expenseModal').modal('toggle');
        }

        //delete the record
        function deleteExpense(id) {
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
                        url: "/expense/" + id,
                        data: {_token: "{{csrf_token()}}", _method: 'delete'},
                        success: function (data) {
                            if (data.message == 'succeed') {//remove item from the list
                                $('a[href*="deleteExpense(' + id + ')"]').parent().parent().fadeOut(777, function () {
                                    $(this).remove();
                                });
                                toastr.success('Success! Report has been deleted!');
                            } else {
                                toastr.warning('Failed! Fail to delete the record!' + data.message);
                            }
                        },
                        error: function (data) {
                            toastr.warning('Failed! Fail to delete the record!' + data);
                            console.log(data);
                        },
                        dataType: 'json'
                    });
                });

        }

        function updateTotal() {
            total = 0;
            $('.input-numbers').each(function (i, n) {
                var num = parseFloat($(n).val());
                num = isNaN(num) ? 0 : num;
                total += num;
            });
            $('#expense-total').val(total);
        }
    </script>
    <style>
        td a:nth-child(2) {
            color: red;
            margin-left: 1.5em;
        }
    </style>
@endsection()