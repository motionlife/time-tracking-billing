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
        <select class="selectpicker show-tick form-control form-control-sm" data-width="fit"
                id="consultant-select"
                data-live-search="true">
            <option value="" data-icon="glyphicon-user" selected>Consultant</option>
            @foreach(\newlifecfo\Models\Consultant::all() as $consultant)
                <option value="{{$consultant->id}}" {{Request('conid')==$consultant->id?'selected':''}}>{{$consultant->fullname()}}</option>
            @endforeach
        </select>
    @endif
    <input class="date-picker form-control" id="start-date" size="10"
           placeholder="&#xf073; Start Day"
           value="{{Request('start')}}"
           type="text"/>
    <span>-</span>
    <input class="date-picker form-control" id="end-date" size="10" placeholder="&#xf073; End Day"
           value="{{Request('end')}}" type="text"/>
    <a href="javascript:void(0)" type="button" class="btn btn-info"
       id="filter-button">Filter</a>
</div>
