@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-title row" style="margin-left: 1em">
                    <h3>Registered Users</h3>
                    <h5>total:{{$users->total()}}</h5>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name <a href="?fo={{Request::get('fo')=="1"?"0":"1"}}"><i class="fa fa-sort" aria-hidden="true"></i></a></th>
                            <th>Last Name <a href="?lo={{Request::get('lo')=="1"?"0":"1"}}"><i class="fa fa-sort" aria-hidden="true"></i></a></th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Set Role</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $offset = ($users->currentPage() - 1) * $users->perPage() + 1;?>
                        @foreach($users as $user)
                            <tr data-id="{{$user->id}}">
                                <th scope="row">{{$loop->index+$offset}}</th>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td><a href="mailto:{{$user->email}}"><i class="fa fa-envelope" aria-hidden="true">&nbsp;{{$user->email}}</i></a></td>
                                <td>{{$user->getType()}}</td>
                                <td><select name="user_role" class="selectpicker show-tick" data-width="fit">
                                        <option value="0" @if($user->unRecognized()) selected
                                                data-content="<span class='label label-danger'>Unrecognized</span>"@endif>
                                            Unrecognized
                                        </option>
                                        <option value="1" @if($user->isInactive()) selected
                                                data-content="<span class='label label-default'>Inactive User</span>"@endif>
                                            Inactive User
                                        </option>
                                        <option value="2" @if($user->isNormalUser()) selected
                                                data-content="<span class='label label-primary'>Normal User</span>"@endif>
                                            Normal User
                                        </option>
                                        <option value="3" @if($user->isLeaderCandidate()) selected
                                                data-content="<span class='label label-success'>Leader Candidate</span>"@endif>
                                            Leader Candidate
                                        </option>
                                        <option value="4" @if($user->isManager()) selected
                                                data-content="<span class='label label-info'>General Admin</span>"@endif>
                                            General Admin
                                        </option>
                                        <option value="5" @if($user->isSuperAdmin()) selected
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
                <div class="pull-right pagination">
                    {{ $users->appends(Request::all())->withPath('/admin/user')->links() }}
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
            $('td:last-child a').on('click', function () {
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
                            var style = v == "0" ? "danger'>Unrecognized" : v == "1" ? "default'>Inactive User" : v == "2" ? "primary'>Normal User" : v == "3" ? "success'>Leader Candidate" : v == "4" ? "info'>General Admin" : v == "5" ? "warning'>Super Admin":'';
                            select.find(':selected').data('content', "<span class='label label-" + style+"</span>");
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
        td:last-child a {
            color: red;
        }
        .bootstrap-select .btn {
            border-style: dashed;
            margin-bottom:2px;
        }
    </style>
@endsection
