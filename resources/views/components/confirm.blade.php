@php
    $isHour = Request::is('approval/hour');
    $stat =  $isHour?$confirm['reports']->reduce(function ($carry, $hour) {
                return [$carry[0] + $hour->billable_hours, $carry[1] + $hour->earned(), $carry[2] + $hour->billClient()];
            }):$confirm['reports']->sum(function ($expense) {
                return $expense->total();
            });
@endphp
<div class="panel-body">
    <div class="alert alert-success">
        <div class="confirm-stat row">
            <table class="table">
                <thead>
                <tr>
                    <th>Period</th>
                    <th>Listed Reports</th>
                    <th>Consultants</th>
                    @if($isHour)
                        <th>Total Billable Hours</th>
                        <th>Payroll Amount</th>
                        <th>Hours Billing</th>
                    @else
                        <th>Total Expenses</th>
                        <th>Expense Billing</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <span class="label label-primary">{{$confirm['startOfLast']->toFormattedDateString().' - '.$confirm['endOfLast']->toFormattedDateString()}}</span>
                    </td>
                    <td><span class="badge bg-success">{{$reports->total()}}</span></td>
                    <td>{{$confirm['reports']->pluck('consultant_id')->unique()->count()}}</td>
                    @if($isHour)
                        <td>{{$stat[0]}} Hours</td>
                        <th>${{number_format($stat[1],2)}}</th>
                        <th>${{number_format($stat[2],2)}}</th>
                    @else
                        <td>${{number_format($stat,2)}}</td>
                        <th>${{number_format($stat,2)}}</th>
                    @endif
                </tr>
                </tbody>
            </table>
        </div>
        <div>
            <i class="fa fa-exclamation-triangle"></i>
            After making sure there are no mistakes about the listed <strong>{{$reports->total()}}</strong> reports then
            click the confirm button.
            <div class="form-group pull-right">
                <button id="confirm_button" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing"
                        class="btn btn-primary" {{$reports->total()==0?'disabled':''}}>Confirm
                </button>
                <i>&nbsp;</i>
                <a href="{{url()->current().'?summary=1'}}" class="btn btn-default">Back</a>
            </div>
        </div>
    </div>
</div>
@section('confirm_module')
    <script>
        $(function () {
            $('#confirm_button').on('click', function () {
                var confirm = $(this);
                confirm.button('loading');
                $.ajax({
                    data: 'submit=confirm',
                    success: function (feedback) {
                        if (feedback.code === 7) {
                            swal({
                                    title: "Success!",
                                    text: "The listed reports have been confirmed by you.",
                                    type: "success",
                                    confirmButtonColor: "#5adb76",
                                    confirmButtonText: "OK"
                                },
                                function () {
                                    location.reload();
                                });
                        } else {
                            toastr.error('Confirmation Failed, ' + feedback.message);
                        }
                    },
                    complete: function () {
                        confirm.button('reset');
                    },
                    error:function () {
                        toastr.error('Confirmation failed, something went wrong.');
                    },
                    dataType:'json'
                });
            });
        });
    </script>
@endsection