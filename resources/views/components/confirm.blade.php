@if($confirm)
    <div class="alert alert-info alert-dismissible" style="font-size: 1.2em">
        <a class="panel-close close" data-dismiss="alert">×</a>
        <i class="fa fa-exclamation-triangle"></i>
        You have <span class="badge bg-success">X</span> unconfirmed time reports in last paying period.<span class="label label-success">{{$confirm['startOfLast']->toFormattedDateString().' - '.$confirm['endOfLast']->toFormattedDateString()}}</span><a
                href="?confirm=1" class="btn btn-primary">Confirm All</a>
    </div>
@endif