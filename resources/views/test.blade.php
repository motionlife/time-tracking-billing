@extends('layouts.app')

@section('content')
    <div class="container">
        <?php
        $j = 0;
        ?>

        <div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Billable Hours</th>
                    <th>Non-billable Hours</th>
                    <th>Total Payroll</th>
                    <th>Reference</th>
                    <th>Total Expense</th>
                </tr>
                </thead>
                <tbody>
                @foreach($consultants as $i=>$consul)
                    <tr>
                        <th scope="row">{{$i+1}}</th>
                        <td>{{$consul->fullname()}}</td>
                        <td>{{$result[$i]['totalbh']}}</td>
                        <td>{{$result[$i]['totalnbh']}}</td>
                        <td><strong>${{number_format($result[$i]['totalpay'],2)}}</strong></td>
                        <td>{{$result[$i]['totalpay']?'$'.number_format($csv[$consul->fullname()],2):''}}</td>
                        <td>${{number_format($result[$i]['totalexpense'],2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <table class="table table-striped">
                <thead>

                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Hours Bill</th>
                    <th>Expenses Bill</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>

                @foreach($clients as $i=> $client)
                    <tr>
                        <th scope="row">{{$i+1}}</th>
                        <td>{{$client->name}}</td>
                        <td>${{number_format($bills[$i]['hoursBill'],2)}}</td>
                        <td>${{number_format($bills[$i]['expensesBill'],2)}}</td>
                        <td><strong>${{number_format($bills[$i]['hoursBill']+$bills[$i]['expensesBill'],2)}}</strong></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection