@extends('layouts.app')
@section('content')
    @php $admin = Request::is('admin/expense'); @endphp
    <div class="main-content" xmlns:javascript="https://www.w3.org/1999/xhtml">
        <div class="container-fluid">
            {{--Begin of Modal--}}
            <div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-labelledby="expenseModalLabel"
                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="expenseModalLabel">Expense Detail</h3>
                            <div class="row" style="margin: -1em -1em;color:#a2ebff;">
                                <div class="col-md-8">
                                    <h2 id="consultant-name"></h2>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <form action="" id="expense-form">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <div class="panel-body">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client and Engagement:</span>
                                        <select id="client-engagement" class="selectpicker" data-width="auto"
                                                name="eid"
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
                                        <input class="form-control" id="input-receipts" name="receipts[]" type="file"
                                               multiple>
                                    </div>
                                    <br>
                                    <textarea id="description" class="form-control" name="description"
                                              placeholder="description"
                                              rows="5"></textarea>
                                    <br>
                                    @if($admin)
                                        <div style=" border-style: dotted;color:#33c0ff; padding: .3em .3em .3em .3em;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="fancy-radio">
                                                        <input name="review_state" value="1" type="radio">
                                                        <span><i></i>Endorse Report</span>
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="fancy-radio">
                                                        <input name="review_state" value="2"
                                                               type="radio">
                                                        <span><i></i>Recommend Re-submit</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <input class="form-control" name="feedback" id="expense-feedback"
                                                   placeholder="feedback" type="text">
                                        </div>
                                    @endif
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
                        <h3 class="panel-title">{{$admin?'Reported Expense Pool':'Expenses History'}}</h3>
                        <p class="panel-subtitle">{{$expenses->total()}} results</p>
                        @if(!$admin)
                            <a class="btn btn-success update-pro" id="add-expense" href="javascript:void(0)"
                               title="Report Your Expense"><i class="fa fa-plus" aria-hidden="true"></i>
                                <span>Add New</span></a>
                        @endif
                    </div>

                    <div class="panel-body col-md-9">
                        <div class="form-inline pull-right" style="font-family:FontAwesome;">
                            <div class="form-group">
                                <select class="selectpicker show-tick" data-width="fit" id="client-engagements"
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
                                <select class="selectpicker show-tick" data-width="fit" id="consultant-select"
                                        data-live-search="true">
                                    <option value="" data-icon="glyphicon-user" selected>Consultant</option>
                                    @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                                        <option value="{{$consultant->id}}" {{Request('conid')==$consultant->id?'selected':''}}>{{$consultant->fullname()}}</option>
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
                    <table class="table table-striped table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Engagement</th>
                            <th>Client</th>
                            <th>Company Paid</th>
                            <th>Report Date</th>
                            <th>Total</th>
                            <th>Receipts</th>
                            <th>{{$admin?'Consultant':'Description'}}</th>
                            <th>Status</th>
                            <th>Operate</th>
                        </tr>
                        </thead>
                        <tbody id="main-table">
                        <?php $offset = ($expenses->currentPage() - 1) * $expenses->perPage() + 1;?>
                        @foreach($expenses as $expense)
                            @php
                                $arr = $expense->arrangement;
                                $eng = $arr->engagement;
                                $cname =$arr->consultant->fullname();
                            @endphp
                            <tr>
                                <th scope="row">{{$loop->index+$offset}}</th>
                                <td>{{str_limit($eng->name,22)}}</td>
                                <td>{{str_limit($eng->client->name,22)}}</td>
                                <td>{{$expense->company_paid?"Yes":"No"}}</td>
                                <td>{{$expense->report_date}}</td>
                                <td><strong>${{number_format($expense->total(),2)}}</strong></td>
                                <td>
                                    @foreach($expense->receipts as $receipt)
                                        <a href="#"
                                           data-featherlight="/{{$receipt->filename}}">File{{$loop->index+1}}</a>
                                    @endforeach
                                </td>
                                <td>
                                    @if($admin)
                                        <strong>{{str_limit($cname,25)}}</strong>
                                    @else
                                        {{str_limit($expense->description,37)}}
                                    @endif
                                </td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.10/featherlight.min.js"></script>
    <script>
        var update;//boolean indicate whether user intend to update or report new
        var expid;
        var tr;
        $(function () {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            $('.input-numbers').on('change', function () {
                updateTotal();
            });
            $('#filter-button').on('click', function () {
                var eid = $('#client-engagements').selectpicker('val');
                var conid = $('#consultant-select').selectpicker('val');
                window.location.href = 'expense?eid=' + (eid ? eid : '') + '&conid=' + (conid ? conid : '') +
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
                $('#description').val('');
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
                var formdata = new FormData($(this)[0]);
                formdata.append('_method', update ? 'put' : 'post');
                $.ajax({
                    type: "POST",
                    url: update ? "/expense/" + expid : "/expense",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formdata,
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            if (update) {
                                toastr.success('Success! Expense has been updated!');
                                tr.find('td:nth-child(4)').html(feedback.record.company_paid);
                                tr.find('td:nth-child(5)').html(feedback.record.report_date);
                                tr.find('td:nth-child(6) strong').html('$' + feedback.record.total);
                                tr.find('td:nth-child(7)').empty().append(outputLink(feedback.record.receipts));
                                @if(!$admin)tr.find('td:nth-child(8)').html(feedback.record.description);
                                @endif
                                tr.find('td:nth-child(9) span').removeClass().addClass('label label-' + feedback.record.status[1]).html(feedback.record.status[0]);
                                tr.addClass('update-highlight');//flash to show user that data already been updated
                                setTimeout(function () {
                                    tr.removeClass('update-highlight');
                                }, 2100);
                            } else {
                                //prepend it at the top of the list
                                toastr.success('Success! Expense has been created!');
                                $('<tr><th scope="row">*</th><td>' + feedback.data.ename + '</td><td>' + feedback.data.cname + '</td><td>' + feedback.data.company_paid + '</td><td>' + feedback.data.report_date + '</td><td><strong>$' + feedback.data.total + '</strong></td><td>' + outputLink(feedback.data.receipts) + '</td><td>' + feedback.data.description + '</td><td><span class="label label-' + feedback.data.status[1] + '">' + feedback.data.status[0] + '</span></td><td><a href="javascript:editExpense(' + feedback.data.expid + ')"><i class="fa fa-pencil-square-o"></i></a><a href="javascript:deleteExpense(' + feedback.data.expid + ')"><i class="fa fa-times"></i></a></td></tr>')
                                    .prependTo('#main-table').hide().fadeIn(1500);
                            }
                        } else {
                            toastr.error('Error! Saving failed, code: ' + feedback.code +
                                ', message: ' + feedback.message)
                        }
                    },
                    error: function (feedback) {
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
                    $('#input-hotel').val(data.hotel);
                    $('#input-flight').val(data.flight);
                    $('#input-meal').val(data.meal);
                    $('#input-office-supply').val(data.office_supply);
                    $('#input-car-rental').val(data.car_rental);
                    $('#input-mileage-cost').val(data.mileage_cost);
                    $('#input-other').val(data.other);
                    $('#input-receipts').val('');//Current didn't want them to modify already uploaded receipts
                    $('#description').val(data.description);
                    $('#expense-total').val(data.total);
                    $('#report-update').attr('disabled', data.review_state !== "0");
                    $('#consultant-name').text(data.cname);
                    @if($admin)
                    $("input[name=review_state][value=" + data.review_state + "]").prop('checked', true);
                    if (data.review_state === "0") $("input[name=review_state]").prop('checked', false);
                    $('#expense-feedback').val(data.feedback);
                    @endif
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

        function outputLink(receipts) {
            var result = '';
            $(receipts).each(function (i, name) {
                result += '<a href="#" data-featherlight="/' + name + '">File ' + (i + 1) + '<a/>';
            });
            return result;
        }

    </script>
    <style>
        td a:nth-child(2) {
            color: red;
            margin-left: 1.5em;
        }
    </style>
@endsection()
@section('special-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.10/featherlight.min.css">
@endsection