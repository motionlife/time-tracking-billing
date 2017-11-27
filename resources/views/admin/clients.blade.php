@extends('layouts.app')
@section('content')

    <div class="main-content">
        <div class="container-fluid">
            @php $clients = \newlifecfo\Models\Client::all(); @endphp
            <div class="panel panel-headline">
                <div class="panel-title">
                    <h3>New Life CFP Clients</h3>
                    <h5>total:{{$clients->count()}}</h5>
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
                                        <div>
                                            <p>{{$revenue->year}}:</p>
                                            <p>Revenue:${{$revenue->revenue}}</p>
                                            <p>EBIT:${{$revenue->ebit}}</p>
                                        </div>
                                    @endforeach
                                </td>
                                <td><a href="javascript:void(0)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('my-js')
    <script>
        $(function () {
            toastr.options = {
                "positionClass": "toast-bottom-full-width",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "4000",
                "extendedTimeOut": "900"
            };
        });
    </script>
@endsection

@section('special-css')
@endsection
