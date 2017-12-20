<div class="input-group">
    <span class="input-group-addon"><i class="fa fa-users"></i>&nbsp; Client and Engagement:</span>
    <select id="client-engagement" class="selectpicker show-tick" data-width="auto" data-dropup-auto="false"
            data-live-search="true" name="eid" title="Select the engagements your want report to" required>
        @if(isset($clientIds))
            @foreach($clientIds as $cid=>$engagements)
                <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                    @foreach($engagements as $eng)
                        <option value="{{$eng[0]}}">{{$eng[1]}}</option>
                    @endforeach
                </optgroup>
            @endforeach
        @endif
    </select>
</div>
<br>
<div class="input-group">
    <span class="input-group-addon"><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;Job Position:</span>
    <select class="selectpicker" id="position" name="pid" data-width="auto"
            required></select>
    <span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp; Report Date</span>
    <input class="date-picker form-control" id="report-date" placeholder="mm/dd/yyyy"
           name="report_date" type="text" required/>

</div>
<br>
<div class="input-group">
    <span class="input-group-addon"><i class="fa fa-tasks"></i>&nbsp;Task:</span>
    <select id="task-id" class="selectpicker show-sub-text" data-live-search="true"
            data-width="auto" name="task_id" data-dropup-auto="false"
            title="Please select one of the tasks your did" required>
        @foreach(\newlifecfo\Models\Templates\Taskgroup::all() as $tgroup)
            <?php $gname = $tgroup->name?>
            @foreach($tgroup->tasks as $task)
                <option value="{{$task->id}}"
                        data-content="{{$gname.' <strong>'.$task->description.'</strong>'}}"></option>
            @endforeach
        @endforeach
    </select>
</div>
<br>
<div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-usd"></i>&nbsp;<strong>Billable Hours:</strong></span>
    <input class="form-control" id="billable-hours" name="billable_hours" type="number"
           placeholder="numbers only"
           step="0.1" min="0"
           max="24" required>

    <span class="input-group-addon"><i
                class="fa fa-hourglass-start"></i>&nbsp;Non-billable Hours:</span>
    <input class="form-control" id="non-billable-hours" name="non_billable_hours"
           type="number" step="0.1" min="0"
           placeholder="numbers only">

</div>
<br>
<div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-money"></i>&nbsp;Estimated Income:</span>
    <input type="text" id="income-estimate" disabled value="" class="form-control" data-br="" data-fs="">
</div>
<br>
<textarea id="description" class="form-control notebook" name="description" placeholder="description"
          rows="3"></textarea>
<br>
@if(isset($admin))
    @if($admin)
        <div style=" border-style: dotted;color:#33c0ff; padding: .3em .3em .3em .3em;">
            <div class="row">
                <div class="col-md-6">
                    <label class="fancy-radio">
                        <input name="endorse-or-not" value="1" type="radio">
                        <span><i></i>Endorse Report</span>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="fancy-radio">
                        <input name="endorse-or-not" value="2"
                               type="radio">
                        <span><i></i>Recommend Re-submit</span>
                    </label>
                </div>
            </div>
            <input class="form-control" name="feedback" id="hour-feedback"
                   placeholder="feedback" type="text">
        </div>
    @else
        <div id="feedback-info" style="margin-bottom: -1em"></div>
    @endif
@endif