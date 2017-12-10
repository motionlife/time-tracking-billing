<div class="form-inline pull-right form-group-sm" style="font-family:FontAwesome;">
    <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
            id="client-engagements"
            data-live-search="true">
        <option value="" data-icon="glyphicon-briefcase" selected>Client & Engagement
        </option>
        @foreach($clientIds as $cid=>$engagements)
            <optgroup label="{{newlifecfo\Models\Client::find($cid)->name }}">
                @foreach($engagements as $eng)
                    <option value="{{$eng[0]}}" {{Request('eid')==$eng[0]?'selected':''}}>{{$eng[1]}}</option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    @if($admin)
        <i>&nbsp;</i>
        <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
                id="consultant-select"
                data-live-search="true">
            <option value="" data-icon="glyphicon-user" selected>Consultant</option>
            @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                <option value="{{$consultant->id}}" {{Request('conid')==$consultant->id?'selected':''}}>{{$consultant->fullname()}}</option>
            @endforeach
        </select>
    @endif
    @if(isset($payroll))
        <i>&nbsp;</i>
        <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
                id="state-select"
                data-live-search="true">
            <option value="" data-icon="glyphicon-flag" selected>Status</option>
            <option value="1" {{Request('state')=="1"?'selected':''}}>Approved</option>
            <option value="0" {{Request('state')=="0"?'selected':''}}>Pending</option>
        </select>
    @endif
    <i>&nbsp;</i>
    <input class="date-picker form-control" id="start-date" size="10"
           placeholder="&#xf073; Start Day"
           value="{{Request('start')}}"
           type="text"/>
    <span>-</span>
    <input class="date-picker form-control" id="end-date" size="10" placeholder="&#xf073; End Day"
           value="{{Request('end')}}" type="text"/>
    <i>&nbsp;</i>
    <a href="javascript:filter_resource();" type="button" class="btn btn-info"
       id="filter-button">{{isset($payroll)?'View':'Filter'}}</a>
    <script>
        function filter_resource() {
            var eid = $('#client-engagements').val();
            var conid = $('#consultant-select').val();
            var state = $('#state-select').val();
            var resource = "{{Request::is('hour')||Request::is('admin/hour')?'hour':(Request::is('expense')||Request::is('admin/expense')?'expense':'payroll')}}";
            window.location.href = resource + '?eid=' + (eid ? eid : '') + '&conid=' + (conid===undefined ?'':conid) +
                '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val() + '&state=' + (state===undefined?'':state);
        }
    </script>
</div>
