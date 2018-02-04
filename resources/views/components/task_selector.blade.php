<span class="input-group-addon"><i class="fa fa-tasks"></i>&nbsp;Task:</span>
<select id="{{$dom_id}}" class="selectpicker show-sub-text form-control form-control-sm"
        data-live-search="true"
        data-width="100%" name="task_id" data-dropup-auto="false"
        title="Select your task" required>
    @foreach(\newlifecfo\Models\Templates\Taskgroup::newGroups() as $tgroup)
        <?php $gname = $tgroup->name?>
        <option disabled data-content="────────────────<span class='label label-info'>{{$gname}}</span>────────────────"></option>
        @foreach($tgroup->tasks as $task)
            <option value="{{$task->id}}" data-task="{{$task->description}}"
                    title="{{$gname.' <strong>'.$task->description.'</strong>'}}">{{$task->description}}</option>
        @endforeach
    @endforeach
</select>