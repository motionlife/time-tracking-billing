@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-headline">
                    <div class="panel-heading">
                        <h3 class="panel-title">Working Time Report</h3>
                        <p class="panel-subtitle">Consultant: {{Auth::user()->fullName()}}</p>
                    </div>
                    <div class="panel-body">
                        <div class="input-group">
                            <span class="input-group-addon"><i
                                        class="fa fa-users">&nbsp; Client and Engagement:</i></span>
                            <select class="selectpicker show-tick" data-width="auto" data-live-search="true"
                                    title="Select from the engagements your're currently in">
                                <optgroup label="Picnic">
                                    <option>Mustard</option>
                                    <option>Ketchup</option>
                                    <option>Relish</option>
                                </optgroup>
                                <optgroup label="Camping">
                                    <option>Tent</option>
                                    <option>Flashlight</option>
                                    <option>Toilet Paper</option>
                                </optgroup>
                            </select>
                        </div>
                        <br>
                        <div class="input-group">
                            <input class="date-picker form-control" id="report-date" placeholder="mm/dd/yyyy" value=""
                                   type="text"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp; Report Date</span>
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-tasks">&nbsp;Task:</i></span>
                            <select class="selectpicker" data-live-search="true" data-width="auto"
                                    title="search for your task,pick the closest one">
                                <optgroup label="example">

                                    <option>Hot Dog, Fries and a Soda</option>
                                    <option>Burger, Shake and a Smile</option>
                                    <option>Sugar, Spice and all things nice</option>
                                </optgroup>
                                <optgroup label="computer">
                                    <option>Laravel, Web Development</option>
                                    <option>Spring Framework, Enterprise Application</option>
                                    <option>Machine Learning, Data Science</option>
                                </optgroup>
                            </select>
                        </div>
                        <br>
                        <div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-usd"></i>&nbsp;<strong>Billable Hours:</strong></span>
                            <input class="form-control" type="number" placeholder="numbers only" step="0.1" min="0"
                                   max="24" required>

                            <span class="input-group-addon"><i
                                        class="fa fa-hourglass-start">&nbsp;Non-billable Hours:</i></span>
                            <input class="form-control" type="number" step="0.1" min="0" placeholder="numbers only">

                        </div>
                        <br>
                        <textarea class="form-control " placeholder="description" rows="4"></textarea>
                        <br>
                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-info">Submit</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- TIMELINE -->
                <div class="panel panel-scrolling">
                    <div class="panel-heading">
                        <h3 class="panel-title">Today's Reports</h3>
                        <div class="right">
                            <button type="button" class="btn-toggle-collapse"><i class="lnr lnr-chevron-up"></i>
                            </button>
                            <button type="button" class="btn-remove"><i class="lnr lnr-cross"></i></button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled activity-list">
                            <li>
                                <img src="/img/user1.png" alt="Avatar" class="img-circle pull-left avatar">
                                <p><a href="#">Michael</a> has achieved 80% of his completed tasks <span
                                            class="timestamp">20 minutes ago</span></p>
                            </li>
                            <li>
                                <img src="/img/user2.png" alt="Avatar" class="img-circle pull-left avatar">
                                <p><a href="#">Daniel</a> has been added as a team member to project <a href="#">System
                                        Update</a> <span class="timestamp">Yesterday</span></p>
                            </li>
                            <li>
                                <img src="/img/user3.png" alt="Avatar" class="img-circle pull-left avatar">
                                <p><a href="#">Martha</a> created a new heatmap view <a href="#">Landing Page</a> <span
                                            class="timestamp">2 days ago</span></p>
                            </li>
                            <li>
                                <img src="/img/user4.png" alt="Avatar" class="img-circle pull-left avatar">
                                <p><a href="#">Jane</a> has completed all of the tasks <span class="timestamp">2 days ago</span>
                                </p>
                            </li>
                            <li>
                                <img src="/img/user5.png" alt="Avatar" class="img-circle pull-left avatar">
                                <p><a href="#">Jason</a> started a discussion about <a href="#">Weekly Meeting</a> <span
                                            class="timestamp">3 days ago</span></p>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-primary btn-bottom center-block">Load More</button>
                    </div>
                </div>
                <!-- END TIMELINE -->
            </div>
        </div>
    </div>
@endsection
@section('my-js')
    <script>
        $(function () {
//            $('#filter-button').on('click', function () {
//                var eid = $('#client-engagements').find(":selected").attr('id');
//                window.location.href = '/hour?eid=' + (eid ? eid : '') +
//                    '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
//            });
            $('#report-date').datepicker({
                format: 'mm/dd/yyyy',
                todayHighlight: true,
                autoclose: true,
            });
        });
    </script>
@endsection

@section('special-css')
@endsection
