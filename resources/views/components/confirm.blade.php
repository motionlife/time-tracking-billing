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
            You have unconfirmed time reports in last paying period.
            <div class="form-group pull-right">
                <a href="#" class="btn btn-primary">Confirm</a>
                <a href="{{url()->current().'?summary=1'}}" class="btn btn-default">Back</a>
            </div>
        </div>
    </div>
</div>
