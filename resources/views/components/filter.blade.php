<div class="form-inline pull-right form-group-sm" id="filter-template" style="font-family:FontAwesome;">
    <a href="javascript:reset_select();" class="btn btn-default form-control form-control-sm"
       title="Reset all condition"><i class="fa fa-refresh" aria-hidden="true"></i></a>
    <i>&nbsp;</i>
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
    <i>&nbsp;</i>
    <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
            id="state-select"
            data-live-search="true">
        <option value="" data-icon="glyphicon-flag" selected>Status</option>
        <option value="1" {{Request('state')=="1"?'selected':''}}>Approved</option>
        <option value="0" {{Request('state')=="0"?'selected':''}}>Pending</option>
    </select>
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
            var query = '?eid=' + $('#client-engagements').selectpicker('val') +
                '&state=' + $('#state-select').selectpicker('val') +
                '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
            @if($admin) query += '&conid=' + $('#consultant-select').selectpicker('val');
                    @endif
            var resource = "{{Request::is('hour')||Request::is('admin/hour')?'hour':(Request::is('expense')||Request::is('admin/expense')?'expense':'payroll')}}";
            window.location.href = resource + query;

        }

        function reset_select() {
            $('#filter-template').find('select.selectpicker').selectpicker('val', '');
            $('#filter-template').find('.date-picker').val("").datepicker("update");
            filter_resource();
        }
    </script>
</div>
