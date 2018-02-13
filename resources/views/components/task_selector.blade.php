<span class="input-group-addon"><i class="fa fa-tasks"></i>&nbsp;Task:</span>
<select id="{{$dom_id}}" class="selectpicker task_selector show-sub-text form-control form-control-sm"
        data-live-search="true"
        data-width="100%" name="task_id" data-dropup-auto="false"
        title="Select your task" required {{$withOldTasks?'disabled':''}}>
    @foreach(\newlifecfo\Models\Templates\Taskgroup::getGroups($withOldTasks) as $tgroup)
        <?php $gname = $tgroup->name?>
        <option disabled data-content="────────────────<span class='label label-info'>{{$gname}}</span>────────────────"></option>
        @foreach($tgroup->tasks as $task)
            <option value="{{$task->id}}" data-task="{{$task->description}}"
                    title="{{$gname.' <strong>'.$task->description.'</strong>'}}">{{$task->description}}</option>
        @endforeach
    @endforeach
</select>
@section('task_selector')
    var task_selector = $("#task-id");
    var all_options = task_selector.find("option");
    var commons = task_selector.find("option[title~='Common']");
    var valid_tasks = ["CFO Services","Controller Services","Business Intelligence Services","Investor Services","Tip of the Spear Services","New Life Admin"];
    $('#client-engagement-addrow, #client-engagement').on('changed.bs.select',function (e) {
        var selected = $(this).find('option:selected').text();
        var current_selector = $(this).parents('form:first').find("select.task_selector");
        if(valid_tasks.indexOf(selected)!=-1){
            current_selector.empty().append(all_options.filter("[title~='" + selected + "']"))
                                .append(commons).selectpicker("refresh");
        }else{
            current_selector.empty().append(all_options).selectpicker("refresh");;
        }
    });
@endsection