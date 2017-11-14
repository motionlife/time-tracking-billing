@extends('layouts.app')

@section('content')
    <div class="">
        @foreach($consultants as $i=>$consul)
            <div>
                <h3>{{$consul->fullname()}}</h3>
                <ur>
                    <li>Total Billable Hours:{{$result[$i]['totalbh']}}</li>
                    <li>Total Nob-billable Hours:{{$result[$i]['totalnbh']}}</li>
                    <li>Total Paid:<strong>{{$result[$i]['totalpay']}}</strong></li>
                </ur>
            </div>
            <hr>
        @endforeach
    </div>
@endsection