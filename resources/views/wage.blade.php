@extends('layouts.app')
@section('content')
    @php $admin = true; @endphp
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="row">
                    <div class="panel-heading col-md-3">
                        <h3 class="panel-title">Estimated Payroll</h3>
                        <p class="panel-subtitle">Period: 12/09/2017 - 12/31/2017</p>
                    </div>
                    <div class="panel-body col-md-9">
                        @component('components.filter',['clientIds'=>$clientIds,'admin'=>$admin,'payroll'=>true])
                        @endcomponent
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">${{number_format($hourIncome,2)}}</span>
                                    <span class="title">Hours Income</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">${{number_format($expenseIncome,2)}}</span>
                                    <span class="title">Expenses</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">$99999</span>
                                    <span class="title">Business Development</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-money"></i></span>
                                <p>
                                    <span class="number"><strong>$99999</strong></span>
                                    <span class="title"><strong>Total</strong></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 1.5em;padding-right: 1.5em;">
                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                            <ul class="nav" role="tablist">
                                <li class="active"><a href="#tab-bottom-left1" role="tab" data-toggle="tab">Engagement
                                        Income</a></li>
                                <li><a href="#tab-bottom-left2" role="tab" data-toggle="tab">Expense<span class="badge">77777</span></a>
                                </li>
                                <li><a href="#tab-bottom-left3" role="tab" data-toggle="tab">Buz Dev Income<span
                                                class="badge bg-danger">9999</span></a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab-bottom-left1">
                                <div class="table-responsive">
                                    <table class="table project-table">
                                        <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Progress</th>
                                            <th>Leader</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><a href="#">Spot Media</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                                        <span>60% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">E-Commerce Site</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="33"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 33%;">
                                                        <span>33% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-warning">PENDING</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="68"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 68%;">
                                                        <span>68% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Wordpress Theme</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="75"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                        <span>75%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"/>
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Redesign Landing Page</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user5.png" alt="Avatar" class="avatar img-circle"/>
                                                <a href="#">Jason</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab-bottom-left2">
                                <div class="table-responsive">
                                    <table class="table project-table">
                                        <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Progress</th>
                                            <th>Leader</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><a href="#">Spot Media</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                                        <span>60% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">E-Commerce Site</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="33"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 33%;">
                                                        <span>33% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-warning">PENDING</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="68"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 68%;">
                                                        <span>68% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Wordpress Theme</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="75"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                        <span>75%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"/>
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Redesign Landing Page</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user5.png" alt="Avatar" class="avatar img-circle"/>
                                                <a href="#">Jason</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab-bottom-left3">
                                <div class="table-responsive">
                                    <table class="table project-table">
                                        <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Progress</th>
                                            <th>Leader</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><a href="#">Spot Media</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                                        <span>60% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">E-Commerce Site</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="33"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 33%;">
                                                        <span>33% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-warning">PENDING</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="68"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 68%;">
                                                        <span>68% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Wordpress Theme</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="75"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                        <span>75%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle">
                                                <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"/>
                                                <a href="#">Antonius</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Redesign Landing Page</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user5.png" alt="Avatar" class="avatar img-circle"/>
                                                <a href="#">Jason</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('my-js')
    <script>
       $( function(){
           $('.date-picker').datepicker(
               {
                   format: 'mm/dd/yyyy',
                   todayHighlight: true,
                   autoclose: true
               }
           );
        });

    </script>
@endsection

@section('special-css')
@endsection
