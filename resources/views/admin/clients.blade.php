@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            @if($client)
                @php $create = Request::get('new')==1; @endphp

                <div class="panel panel-headline">
                    <div class="panel-title" style="margin-left: 1em">
                        <h3>{{$create?'Create A New Client':'Update Client Information'}}</h3>
                    </div>
                    <form id="client-form" action="?action={{$create?'create':'update&cid='.$client->id}}" method="post">
                        {{csrf_field()}}
                        <div class="panel-body">
                            <label for="name">Client Name: </label>
                            <input type="text" name="name" value="{{$create?'':$client->name}}" class="form-control" placeholder="client name" required>
                            <br>
                            <label for="industry_id">Industry:</label>
                            <select name="industry_id" id="industry-id" data-live-search="true"
                                    class="selectpicker form-control" title="select client industry" required>
                                @foreach(\newlifecfo\Models\Templates\Industry::all() as $industry)
                                    <option value="{{$industry->id}}" {{$create?'':($industry->id==$client->industry_id?'selected':'')}}>{{$industry->name}}</option>
                                @endforeach
                            </select>
                            <br>
                            <br>
                            <label for="buz_dev_person_id">Business Develop Person:</label>
                            <select name="buz_dev_person_id" id="buz-dev-person-id" data-live-search="true"
                                    class="selectpicker form-control" title="select business develop person" required>
                                <option value="0"  {{$create?'':(0 ==$client->buz_dev_person_id?'selected':'')}}>New Life CFO</option>
                                @foreach(\newlifecfo\Models\Consultant::recognized() as $consultant)
                                    <option value="{{$consultant->id}}" {{$create?'':($consultant->id ==$client->buz_dev_person_id?'selected':'')}}>{{$consultant->fullname()}}</option>
                                @endforeach
                            </select>
                            <br>
                            <br>
                            <label for="outreferrer_id">Outside Referrer:</label>
                            <select name="outreferrer_id" id="outreferrer-id" data-live-search="true"
                                    class="selectpicker form-control" title="select outside referrer" required>
                                <option value="0"  {{$create?'':(0 ==$client->outreferrer_id?'selected':'')}}>N/A</option>
                                @foreach(\newlifecfo\Models\Outreferrer::all() as $out)
                                    <option value="{{$out->id}}"{{$create?'':($out->id==$client->outreferrer_id?'selected':'')}}>{{$out->fullname()}}</option>
                                @endforeach
                            </select>
                            <br>
                            <br>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="complex_structure" value="1" {{$create?'':($client->complex_structure?'checked':'')}}>
                                <span>Is Complex Structure</span>
                            </label>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="messy_accounting_at_begin" value="1" {{$create?'':($client->messy_accounting_at_begin?'checked':'')}}>
                                <span>Messy Accounting At Begin</span>
                            </label>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon">2015 Revenue:</span>
                                <input class="form-control currency" placeholder="2015 revenue" value="{{$create?'':$client->getRevenue(2015,'revenue')}}" type="text" name="revenue2015">
                                <span class="input-group-addon">2015 EBIT:</span>
                                <input class="form-control currency" placeholder="2015 ebit" value="{{$create?'':$client->getRevenue(2015,'ebit')}}" type="text" name="ebit2015">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">2016 Revenue:</span>
                                <input class="form-control currency" placeholder="2016 revenue" value="{{$create?'':$client->getRevenue(2016,'revenue')}}" type="text" name="revenue2016">
                                <span class="input-group-addon">2016 EBIT:</span>
                                <input class="form-control currency" placeholder="2016 ebit" value="{{$create?'':$client->getRevenue(2016,'ebit')}}" type="text" name="ebit2016">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">2017 Revenue:</span>
                                <input class="form-control currency" placeholder="2017 revenue" value="{{$create?'':$client->getRevenue(2017,'revenue')}}" type="text" name="revenue2017">
                                <span class="input-group-addon">2017 EBIT:</span>
                                <input class="form-control currency" placeholder="2017 ebit" value="{{$create?'':$client->getRevenue(2017,'ebit')}}" type="text" name="ebit2017">
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button id="update-create" type="submit" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing" class="btn btn-info">{{$create?'Create':'Update'}}</button>
                            <span>&nbsp;</span><a href="/admin/client" class="btn btn-default">Back</a>
                        </div>
                    </form>
                </div>
            @else
                @php $clients = \newlifecfo\Models\Client::all(); @endphp
                <div class="panel panel-headline">
                    <div class="panel-title row" style="margin-left: 1em">
                        <div class="col-md-3">
                            <h3>New Life CFO Clients</h3>
                            <h5>total:{{$clients->count()}}</h5>
                        </div>
                        <div class="col-md-9" style="margin-top: 1em">
                            <a class="btn btn-success" href="?new=1"><i class="fa fa-plus-square-o"
                                                                        aria-hidden="true"></i>&nbsp;Add
                                New</a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Industry</th>
                                <th>Developed Person</th>
                                <th>Outside Referrer</th>
                                <th>Complex Structure</th>
                                <th>Messy Account at Beginning</th>
                                <th>Revenue and Ebit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr data-id="{{$client->id}}">
                                    <th>{{$loop->index + 1}}</th>
                                    <td>{{$client->name}}</td>
                                    <td>{{$client->industry->name}}</td>
                                    <td>{{$client->dev_by_consultant->fullname()}}</td>
                                    <td>{{$client->outreferrer->fullname()}}</td>
                                    <td>{{$client->complex_structure?'Yes':'No'}}</td>
                                    <td>{{$client->messy_accounting_at_begin?'Yes':'No'}}</td>
                                    <td>
                                        @foreach($client->revenues as $revenue)
                                            <a class="label label-info" data-toggle="popover" data-content=
                                            "<strong>Revenue: </strong>${{number_format($revenue->revenue,2)}}<br>
                                            <strong>EBIT: </strong>${{number_format($revenue->ebit,2)}}">{{$revenue->year}}</a>
                                        @endforeach
                                    </td>
                                    <td><a href="?edit=1&cid={{$client->id}}"><i class="fa fa-pencil-square-o"
                                                                                 aria-hidden="true"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
@section('my-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.1.0/autoNumeric.min.js"></script>
    <script>
        AutoNumeric.multiple('input.currency',{'currencySymbol':'$','unformatOnSubmit':true});
        $(function () {
            $('[data-toggle="popover"]').popover({
                html: true,
                container: '.main-content',
                placement: 'top',
                trigger: 'hover'
            });
            $('#client-form').on('submit',function () {
                $('#update-create').button('loading');
            });
            @if ($feedback==1)
                toastr.success('Operation succeed!');
            @endif
        });
    </script>
@endsection
@section('special-css')
    <style>
        tr td a.label-info:last-child {
            margin-left: 0.5em;
        }
        input.currency {
            text-align:right;
        }
    </style>
@endsection
