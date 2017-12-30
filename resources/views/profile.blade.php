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
                    Please keep password and password confirmation blank if you do not want to change it
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
            <div class="col-md-6 role-info">
                <div class="alert alert-success alert-dismissible">
                    <a class="panel-close close" data-dismiss="alert">×</a>
                    <i class="fa fa-info"></i>
                    Please fill your correct information so that it can be verified by admin and grant you the access to
                    the system.
                </div>
                @if($user->getType()=='Consultant')
                    <h3>Consultant info</h3>
                    <form class="form-horizontal" id="con-info-form">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Standard Rate $/hr:</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="standard_rate" type="number"
                                       value="{{$user->consultant->standard_rate}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Standard Percentage %:</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="standard_percentage" type="number" max="100"
                                       value="{{$user->consultant->standard_percentage*100}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">IS NLCFO Employee:</label>
                            <div class="col-lg-8">
                                <div class="ui-select">
                                    <select id="is_employee" name="isEmployee" class="selectpicker" data-width="100%">
                                        <option value="0" }>No</option>
                                        <option value="1" {{$user->consultant->isEmployee?"selected":""}}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Inactive:</label>
                            <div class="col-md-8">
                                <div class="ui-select">
                                    <select id="inactive" name="inactive" class="selectpicker" data-width="100%">
                                        <option value="0" }>No</option>
                                        <option value="1" {{$user->consultant->inactive?"selected":""}}>Yes</option>
                                    </select>
                                </div>
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
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-8">
                                <input type="submit" class="btn btn-primary" value="Save Changes">
                                <span></span>
                                <input type="reset" class="btn btn-default" value="Reset">
                            </div>
                        </div>
                    </form>
                @elseif($user->getType()=='Client')
                    <h3>Client info</h3>
                    <div>Not Implemented</div>
                @else
                    <h3>Other User Type info</h3>
                    <div>Not Implemented</div>
                @endif
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
            $('#con-info-form').on('submit', function (e) {
                e.preventDefault();
                formdata = $(this).serializeArray();
                formdata.push({name: '_token', value: "{{csrf_token()}}"});
                $.post({
                    url: "/profile?update=business",
                    data: formdata,
                    success: function (feedback) {
                        if (feedback.code == 7) {
                            toastr.success('Success! Business information has been updated');
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
