@extends('layouts.app')

@section('content')
    <div class="">
        <?php
        $j=0;
        ?>
        @foreach($consultants as $i=>$consul)
            <div>
                <h3>{{$consul->fullname()}}</h3>
                <ur>
                    <li>Total Billable Hours:{{$result[$i]['totalbh']}}</li>
                    <li>Total Nob-billable Hours:{{$result[$i]['totalnbh']}}</li>
                    <li>Total Paid:<strong>${{number_format($result[$i]['totalpay'],2)}}</strong></li>
                    @if($result[$i]['totalpay'])
                        <li>Reference:<strong>${{number_format($csv[$j++],2)}}</strong></li>
                    @endif
                </ur>
            </div>
            <hr>
        @endforeach
    </div>
@endsection