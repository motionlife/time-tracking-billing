@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title">Estimated Payroll</h3>
                    <p class="panel-subtitle">Period: 12/09/2017 - 12/31/2017</p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-usd"></i></span>
                                <p>
                                    <span class="number">$66666</span>
                                    <span class="title">Billable Hours</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-hourglass"></i></span>
                                <p>
                                    <span class="number">$77777</span>
                                    <span class="title">Non-billable Hours</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-users"></i></span>
                                <p>
                                    <span class="number">$88888</span>
                                    <span class="title">Engagement(s)</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-taxi"></i></span>
                                <p>
                                    <span class="number">$99999</span>
                                    <span class="title">Expense</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 1.5em;padding-right: 1.5em;">
                        <div class="custom-tabs-line tabs-line-bottom left-aligned">
                            <ul class="nav" role="tablist">
                                <li class="active"><a href="#tab-bottom-left1" role="tab" data-toggle="tab">Engagement Income</a></li>
                                <li><a href="#tab-bottom-left2" role="tab" data-toggle="tab">Expense<span class="badge">7</span></a></li>
                                <li><a href="#tab-bottom-left3" role="tab" data-toggle="tab">Buz Dev Income<span class="badge bg-danger">9</span></a></li>
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
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                                        <span>60% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle"> <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">E-Commerce Site</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%;">
                                                        <span>33% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"> <a href="#">Antonius</a></td>
                                            <td><span class="label label-warning">PENDING</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100" style="width: 68%;">
                                                        <span>68% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"> <a href="#">Antonius</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Wordpress Theme</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                        <span>75%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle"> <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle" /> <a href="#">Antonius</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Redesign Landing Page</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user5.png" alt="Avatar" class="avatar img-circle" /> <a href="#">Jason</a></td>
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
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                                        <span>60% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle"> <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">E-Commerce Site</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%;">
                                                        <span>33% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"> <a href="#">Antonius</a></td>
                                            <td><span class="label label-warning">PENDING</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100" style="width: 68%;">
                                                        <span>68% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"> <a href="#">Antonius</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Wordpress Theme</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                        <span>75%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle"> <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle" /> <a href="#">Antonius</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Redesign Landing Page</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user5.png" alt="Avatar" class="avatar img-circle" /> <a href="#">Jason</a></td>
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
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                                        <span>60% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle"> <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">E-Commerce Site</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%;">
                                                        <span>33% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"> <a href="#">Antonius</a></td>
                                            <td><span class="label label-warning">PENDING</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100" style="width: 68%;">
                                                        <span>68% Complete</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle"> <a href="#">Antonius</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Wordpress Theme</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                        <span>75%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user2.png" alt="Avatar" class="avatar img-circle"> <a href="#">Michael</a></td>
                                            <td><span class="label label-success">ACTIVE</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Project 123GO</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user1.png" alt="Avatar" class="avatar img-circle" /> <a href="#">Antonius</a></td>
                                            <td><span class="label label-default">CLOSED</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Redesign Landing Page</a></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span>100%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><img src="assets/img/user5.png" alt="Avatar" class="avatar img-circle" /> <a href="#">Jason</a></td>
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
    <script></script>
@endsection

@section('special-css')
@endsection
