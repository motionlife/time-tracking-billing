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
                    <table class="table table-responsive">
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
                                        <option value="0" @if(!$user->isVerified()) selected
                                                data-content="<span class='label label-danger'>Unrecognized</span>"@endif>
                                            Unrecognized
                                        </option>
                                        <option value="1" @if($user->isNormalUser()) selected
                                                data-content="<span class='label label-success'>Normal User</span>"@endif>
                                            Normal User
                                        </option>
                                        <option value="2" @if($user->isManager()) selected
                                                data-content="<span class='label label-info'>General Admin</span>"@endif>
                                            General Admin
                                        </option>
                                        <option value="3" @if($user->isSuperAdmin()) selected
                                                data-content="<span class='label label-warning'>Super Admin</span>"@endif>
                                            Super Admin
                                        </option>
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
        $(function () {
            var previous;
            toastr.options = {
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "700",
                "timeOut": "2000",
                "extendedTimeOut": "700"
            };
            $('tr a').on('click', function () {
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
                            url: '/admin/user',
                            data: {_token: "{{csrf_token()}}", action: 'delete', uid: uid},
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
                                toastr.error('An error happened.' + e.message);
                            },
                            dataType: 'json'
                        });
                    });
            });
            $('tr select').on('change', function () {
                var select = $(this);
                var role = $(this).val();
                var tr = $(this).parent().parent().parent();
                var uid = tr.attr('data-id');
                $.post({
                    url: '/admin/user',
                    data: {_token: "{{csrf_token()}}", action: 'update', uid: uid, role: role},
                    success: function (feedback) {
                        if (feedback.code === 7) {
                            toastr.success('Update user success!');
                            var v = select.val();
                            var style = v == "0" ? "danger'>Unrecognized</span>" : v == "1" ? "success'>Normal User</span>" : v == "2" ? "info'>General Admin</span>" : v == "3" ? "warning'>Super Admin</span>" : "default'";
                            select.find(':selected').data('content', "<span class='label label-" + style);
                            select.find('option[value=' + previous + ']').data('content', '');
                            select.selectpicker('refresh');
                        } else {
                            toastr.warning('Update failed, no authorization.');
                            select.selectpicker('val', previous);
                        }
                    },
                    error: function (e) {
                        toastr.error('An error happened.' + e.message);
                        select.selectpicker('val', previous);
                    },
                    dataType: 'json'
                });
            }).on('shown.bs.select', function () {
                previous = $(this).val();
                $(this).selectpicker('refresh');
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
