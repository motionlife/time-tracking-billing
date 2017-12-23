<div class="form-inline pull-right form-group-sm" id="filter-template" style="font-family:FontAwesome;">
    <a href="javascript:reset_select();" class="btn btn-default form-control form-control-sm"
       title="Reset all condition"><i class="fa fa-refresh" aria-hidden="true"></i></a>
    <i>&nbsp;</i>
    <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
            id="client-engagements" title="&#xf0b1; Engagement" data-live-search="true" data-selected-text-format="count" multiple>
        @foreach($clientIds as $cid=>$engagements)
            @php $cname=newlifecfo\Models\Client::find($cid)->name;@endphp
            <optgroup label="" data-subtext="<a href='#' data-id='{{$engagements->map(function($e){return $e[0];})}}' class='group-client-name'><span class='label label-info'><strong>{{$cname}}</strong></span></a>">
                @foreach($engagements as $eng)
                    <option data-tokens="{{$cname.' '.$eng[1]}}" value="{{$eng[0]}}" {{in_array($eng[0],explode(',',Request('eid')))?'selected':''}}>{{$eng[1]}}</option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    @if($admin)
        <i>&nbsp;</i>
        <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
                id="consultant-select" title="&#xf007; Consultant"
                data-live-search="true">
            @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                <option value="{{$consultant->id}}" {{Request('conid')==$consultant->id?'selected':''}}>{{$consultant->fullname()}}</option>
            @endforeach
        </select>
    @endif
    <i>&nbsp;</i>
    <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
            id="state-select" title="&#xf024; Status"
            data-live-search="true">
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
    <a href="javascript:filter_resource();" type="button" class="btn btn-info btn-sm"
       id="filter-button">{{isset($payroll)?'View':'Filter'}}</a>
    @section('filter-module')
        <script>
            function filter_resource() {
                var query = '?eid=' + $('#client-engagements').selectpicker('val') +
                    '&state=' + $('#state-select').selectpicker('val') +
                    '&start=' + $('#start-date').val() + '&end=' + $('#end-date').val();
                @if($admin) query += '&conid=' + $('#consultant-select').selectpicker('val');
                @endif
                    window.location.href = "{{$target}}" + query;

            }

            function reset_select() {
                $('#filter-template').find('select.selectpicker').selectpicker('val', '');
                $('#filter-template').find('.date-picker').val("").datepicker("update");
                filter_resource();
            }

            $(function () {
                var groupClientNameSelected;
                $('.group-client-name').on('click', function () {
                    groupClientNameSelected = groupClientNameSelected === $(this).data('id') ? '' : $(this).data('id');
                    $('#client-engagements').selectpicker('val', groupClientNameSelected);
                });
            });
        </script>
    @endsection
</div>
