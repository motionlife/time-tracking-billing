@if($admin)
    <div style=" border-style: dotted;color:#33c0ff; padding: .3em .3em .3em .3em;">
        <div class="row">
            <div class="col-md-6">
                <label class="fancy-radio">
                    <input name="review_state" value="1" type="radio">
                    <span><i></i><strong>Approved</strong></span>
                </label>
            </div>
            <div class="col-md-6">
                <label class="fancy-radio">
                    <input name="review_state" value="2"
                           type="radio">
                    <span><i></i><strong>Rejected</strong></span>
                </label>
            </div>
        </div>
        <input class="form-control" name="feedback" id="{{$report_id}}"
               placeholder="feedback" type="text">
    </div>
@else
    <div id="feedback-info" style="margin-bottom: -1em"></div>
@endif