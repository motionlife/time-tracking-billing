@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="container-fluid">
            <h1>Client Management</h1>
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

            $('#report-date').datepicker({
                format: 'mm/dd/yyyy',
                todayHighlight: true,
                autoclose: true
            }).datepicker('setDate', new Date());
        });

    </script>
@endsection

@section('special-css')
@endsection
