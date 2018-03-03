<select id="{{$dom_id}}"
        class="selectpicker show-tick form-control form-control-sm" data-width="100%"
        data-dropup-auto="false" data-live-search="true"  name="eid" title="Select the engagement" required>
    @if(isset($clientIds))
        @foreach($clientIds as $cid=>$engagements)
            @php $cname=newlifecfo\Models\Client::find($cid)->name;@endphp
            <optgroup label="" data-label="{{$cname}}"
                      data-subtext="<a href='#' class='group-client-name'><span class='label label-success'><strong>{{$cname}}</strong></span></a>">
                @foreach($engagements as $eng)
                    @if(\newlifecfo\Models\Engagement::find($eng[0])->isActive())
                    <option value="{{$eng[0]}}" data-tokens="{{$cname.' '.$eng[1]}}" title="{{'<strong>'.$cname.'</strong> '.$eng[1]}}">{{$eng[1]}}</option>
                    @endif
                @endforeach
            </optgroup>
        @endforeach
    @endif
</select>