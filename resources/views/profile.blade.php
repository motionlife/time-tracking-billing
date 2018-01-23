@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Edit Profile</h1>
        <hr>
        <div class="row">
            @php $user = \Illuminate\Support\Facades\Auth::user(); @endphp
            <div class="col-md-6 personal-info">
                <div class="alert alert-success alert-dismissible">
                    <a class="panel-close close" data-dismiss="alert">×</a>
                    <i class="fa fa-info"></i>
                    Please leave the "Password" and "Confirm password" field blank if you do not want to change it
                </div>
                <h3>Account info</h3>
                <form class="form-horizontal" id="account-info-form">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">First name:</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="first_name" type="text" value="{{$user->first_name}}"
                                   required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Last name:</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="last_name" type="text" value="{{$user->last_name}}"
                                   required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email:</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="email" name="email" value="{{$user->email}}" disabled="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Phone:</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="phone" type="text"
                                   value="{{$user->consultant->contact->phone}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">User Role:</label>
                        <div class="col-lg-8">
                            <div class="ui-select">
                                <select id="user_role_select" class="selectpicker" data-width="100%" disabled>
                                    <option value="{{$user->role}}"
                                            selected>{{$user->getType()}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Password:</label>
                        <div class="col-md-8">
                            <input class="form-control" name="password" type="password" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Confirm password:</label>
                        <div class="col-md-8">
                            <input class="form-control" type="password" name="password_confirmation" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8">
                            <input type="submit" id="account-info-submit" class="btn btn-primary" value="Save Changes">
                            <span></span>
                            <input type="reset" class="btn btn-default" value="Reset">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <hr>

@endsection
@section('my-js')
    <script>
        $(function () {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            $('#account-info-submit').on('click', function (e) {
                e.preventDefault();
                formdata = $('#account-info-form').serializeArray();
                formdata.push({name: '_token', value: "{{csrf_token()}}"});
                $.post({
                    url: "/profile?update=account",
                    data: formdata,
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            toastr.success('Success! Account information has been updated');
                        } else {
                            toastr.error('Error! Change failed.' + feedback.message);
                        }
                    },
                    error: function (feedback) {
                        toastr.error('Oh NOooooooo...' + feedback.message);
                    },
                    dataType: 'json'
                });

                return false;
            });
        });
    </script>
@endsection
