@extends('layouts.app')
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="panel panel-headline">
                <div class="panel-title">
                    <h3>Registered Users</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive" style="width: 80%;">
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
                        @foreach(\newlifecfo\User::all() as $user)
                            <tr>
                                <th>{{$loop->index + 1}}</th>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->getType()}}</td>
                                <td class="{{$user->getRoleClass()}}"><select name="user_role" class="selectpicker">
                                        <option value="0" {{!$user->isVerified()?"selected":""}}>Unrecognized</option>
                                        <option value="1" {{$user->isNormalUser()?"selected":""}}>Normal User</option>
                                        <option value="2" {{$user->isManager()?"selected":""}}>General Admin</option>
                                        <option value="3" {{$user->isSuperAdmin()?"selected":""}}>Super Admin</option>
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
    <style>
        td a{
            color:red;
        }
        td.unrecognized{
            border: 2px solid #fff743;

        }
        td.normal-user{
            border: 2px solid #63ff34;
        }
        td.general-admin{
            border: 2px solid #4bb3ff;
        }
        td.super-admin{
            border: 2px solid #ff040c;
        }
    </style>
@endsection
