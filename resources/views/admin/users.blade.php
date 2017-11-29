@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            @php $users = \newlifecfo\User::all(); @endphp
            <div class="panel panel-headline">
                <div class="panel-title">
                    <h3>Registered Users</h3>
                    <h5>total:{{$users->count()}}</h5>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive" style="width: 87%;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Set Role</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr data-id="{{$user->id}}">
                                <th>{{$loop->index + 1}}</th>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->getType()}}</td>
                                <td><select name="user_role" class="selectpicker show-tick" data-width="auto">
                                        <option value="0"
                                                {{!$user->isVerified()?"selected":""}}  data-content="<span class='label label-danger'>Unrecognized</span>"></option>
                                        <option value="1"
                                                {{$user->isNormalUser()?"selected":""}} data-content="<span class='label label-success'>Normal User</span>"></option>
                                        <option value="2"
                                                {{$user->isManager()?"selected":""}} data-content="<span class='label label-info'>General Admin</span>"></option>
                                        <option value="3"
                                                {{$user->isSuperAdmin()?"selected":""}} data-content="<span class='label label-warning'>Super Admin</span>"></option>
                                    </select></td>
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
        var previous;
        $(function () {
            toastr.options = {
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "700",
                "timeOut": "2000",
                "extendedTimeOut": "700"
            };
            $('tbody tr a').on('click', function () {
                var tr = $(this).parent().parent();
                var uid = tr.attr('data-id');
                swal({
                        title: "Are you sure?",
                        text: "This user account will be deleted if continue!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!"
                    },
                    function () {
                        $.post({
                            url: '/admin/users',
                            data: {_token: "{{csrf_token()}}", action: 'delete',uid:uid},
                            success: function (feedback) {
                                if (feedback.code == 7) {
                                    toastr.success('Delete user success!');
                                    //remove item
                                    tr.fadeOut(700, function () {
                                        $(this).remove();
                                    });
                                } else {
                                    toastr.warning('Delete failed, no authorization.');
                                }
                            },
                            error: function (e) {
                                toastr.error('An error happened.'+e.message);
                            },
                            dataType:'json'
                        })
                    });
            });
            $('tbody tr select').on('change',function () {
                var select = $(this);
                var role = $(this).val();
                var tr = $(this).parent().parent().parent();
                var uid = tr.attr('data-id');
                $.post({
                    url: '/admin/users',
                    data: {_token: "{{csrf_token()}}", action: 'update',uid:uid,role:role},
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            toastr.success('Update user success!');
                        } else {
                            toastr.warning('Update failed, no authorization.');
                            select.selectpicker('val',previous);
                        }
                    },
                    error: function (e) {
                        toastr.error('An error happened.'+e.message);
                        select.selectpicker('val',previous);
                    },
                    dataType:'json'
                })
            }).on('shown.bs.select',function () {
                previous = $(this).val();
            });
        });

    </script>
@endsection

@section('special-css')
    <style>
        td a {
            color: red;
        }
    </style>
@endsection
