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
                    <form method="POST" id="hour-form" action="/hour">
                        <div class="panel-body">
                            <div class="input-group">
                            <span class="input-group-addon"><i
                                        class="fa fa-users">&nbsp; Client and Engagement:</i></span>
                                <select id="client-engagements" class="selectpicker show-tick" data-width="auto"
                                        data-live-search="true" name="eid"
                                        title="Select from the engagements your're currently in" required>
                                    @foreach($clientIds as $cid=>$engagements)
                                        <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                                            @foreach($engagements as $eng)
                                                <option data-eid="{{$eng[0]}}">{{$eng[1]}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-handshake-o">&nbsp;Job Position:</i></span>
                                <select class="selectpicker" id="position" name="pid" data-width="auto" required></select>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp; Report Date</span>
                                <input class="date-picker form-control" id="report-date" placeholder="mm/dd/yyyy"
                                       name="report_date" type="text" required/>

                            </div>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-tasks">&nbsp;Task:</i></span>
                                <select id="task-id" class="selectpicker show-sub-text" data-live-search="true"
                                        data-width="auto" name="task_id"
                                        title="Please select one of the tasks your did">
                                    @foreach(\newlifecfo\Models\Templates\Taskgroup::all() as $tgroup)
                                        <?php $gname = $tgroup->name?>
                                        @foreach($tgroup->tasks as $task)
                                            <option data-tid="{{$task->id}}"
                                                    data-content="{{$gname.' <strong>'.$task->description.'</strong>'}}"></option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-usd"></i>&nbsp;<strong>Billable Hours:</strong></span>
                                <input class="form-control" id="billable-hours" name="billable_hours" type="number" placeholder="numbers only"
                                       step="0.1" min="0"
                                       max="24" required>

                                <span class="input-group-addon"><i
                                            class="fa fa-hourglass-start">&nbsp;Non-billable Hours:</i></span>
                                <input class="form-control" id="non-billable-hours" name="non_billable_hours" type="number" step="0.1" min="0"
                                       placeholder="numbers only">

                            </div>
                            <br>
                            <textarea id="description" class="form-control" name="description" placeholder="description"
                                      rows="4"></textarea>
                            <br>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary" id="report-button" type="submit"
                                    data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing">Submit
                            </button>
                        </div>
                    </form>
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
            $('#client-engagements').on('change', function () {
                $.ajax({
                    //fetch the corresponding position for him and add option to position option
                    type: "get",
                    url: "/hour",
                    data: {eid: $('#client-engagements').find(":selected").attr('data-eid'), fetch: 'position'},
                    success: function (data) {
                        $('#position').empty();
                        $(data).each(function (i, e) {
                            $('#position').append("<option data-pid=" + e.id + ">" + e.name + "</option>");
                        });
                        $('#position').selectpicker('refresh');
                    }
                });
            });

            $('#hour-form').on('submit', function (e) {
                var eid = $('#client-engagements').find(":selected").attr('data-eid');
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "/hour",
                    data: {
                        _token: token,
                        eid: eid ? eid : '',
                        pid: $('#position').find(":selected").attr('data-pid'),
                        report_date: $('#report-date').val(),
                        task_id: $('#task-id').find(":selected").attr('data-tid'),
                        billable_hours: $('#billable-hours').val(),
                        non_billable_hours: $('#non-billable-hours').val(),
                        description: $('#description').val()
                    },
                    success: function (success) {
                        //notify the user
                        console.log(success);
                    },
                    error: function (error) {
                        //notify the user
                        console.log(error);
                    },
                    beforeSend: function () {
                        //spinner begin to spin
                        $("#report-button").button('loading');
                    },
                    complete: function () {
                        //button spinner stop
                        $("#report-button").button('reset');
                    }
                });
                e.preventDefault();
            });

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
