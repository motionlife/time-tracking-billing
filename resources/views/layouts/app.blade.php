@extends('layouts.html')
@section('wrapper')
    <!-- WRAPPER -->
    <div id="wrapper">
        <!-- NAVBAR -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="brand">
                <a href="/"><img src="/img/logo-dark.png" alt="Klorofil Logo"
                                 class="img-responsive logo"></a>
            </div>
            <div class="container-fluid">
                <div class="navbar-btn">
                    <button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i>
                    </button>
                </div>
                <form class="navbar-form navbar-left">
                    <div class="input-group">
                        <input type="text" value="" class="form-control" placeholder="Search dashboard...">
                        <span class="input-group-btn"><button type="button" class="btn btn-primary">Go</button></span>
                    </div>
                </form>
                <div class="navbar-btn navbar-btn-right">
                    <a class="btn btn-success" href="{{route('hour.create')}}" title="Report Your Time"><i
                                class="fa fa-calendar" aria-hidden="true"></i> <span>REPORT TIME</span></a>
                </div>
                <div id="navbar-menu">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown">
                                <i class="lnr lnr-alarm"></i>
                                <span class="badge bg-danger">7</span>
                            </a>
                            <ul class="dropdown-menu notifications">
                                <li><a href="#" class="notification-item"><span class="dot bg-warning"></span>System
                                        space is almost full</a></li>
                                <li><a href="#" class="notification-item"><span class="dot bg-danger"></span>You have 9
                                        unfinished tasks</a></li>
                                <li><a href="#" class="notification-item"><span class="dot bg-success"></span>Monthly
                                        report is available</a></li>
                                <li><a href="#" class="notification-item"><span class="dot bg-warning"></span>Weekly
                                        meeting in 1 hour</a></li>
                                <li><a href="#" class="notification-item"><span class="dot bg-success"></span>Your
                                        request has been approved</a></li>
                                <li><a href="#" class="more">See all notifications</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="lnr lnr-question-circle"></i> <span>Help</span> <i
                                        class="icon-submenu lnr lnr-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Basic Use</a></li>
                                <li><a href="#">Working With Data</a></li>
                                <li><a href="#">Security</a></li>
                                <li><a href="#">Troubleshooting</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="/img/user.png"
                                                                                            class="img-circle"
                                                                                            alt="Avatar">
                                <span>{{ Auth::user()->fullName() }}</span> <i
                                        class="icon-submenu lnr lnr-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="/profile"><i class="lnr lnr-user"></i> <span>My Profile</span></a></li>
                                <li><a href="#"><i class="lnr lnr-envelope"></i> <span>Message</span></a></li>
                                <li><a href="#"><i class="lnr lnr-cog"></i> <span>Settings</span></a></li>
                                {{--<li><a href="#"><i class="lnr lnr-exit"></i> <span>Logout</span></a></li>--}}
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="lnr lnr-exit"></i><span>Logout</span>
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- END NAVBAR -->
        <!-- LEFT SIDEBAR -->
        <div id="sidebar-nav" class="sidebar">
            <div class="sidebar-scroll">
                <nav>
                    <ul class="nav">
                        <li><a href="/home" class="{{Request::is('home') ?'active':''}}"><i
                                        class="lnr lnr-home"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="#subPages" data-toggle="collapse"
                               class="{{substr(Request::path(),0,4)=='hour' ? 'active':'collapsed '}}"><i
                                        class="lnr lnr-clock"></i> <span>Time</span> <i
                                        class="icon-submenu lnr lnr-chevron-left"></i></a>
                            <div id="subPages" class="collapse {{substr(Request::path(),0,4)=='hour'  ?'in':''}}">
                                <ul class="nav">
                                    <li><a href="{{route('hour.index')}}" class="{{Request::is('hour') ?'active':''}}">Overview</a>
                                    </li>
                                    <li><a href="{{route('hour.create')}}"
                                           class="{{Request::is('hour/create') ?'active':''}}">Report Time</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="{{route('expense.index')}}"
                               class="{{Request::is('expense') ?'active':'collapsed'}}"><i class="fa fa-taxi"
                                                                                           aria-hidden="true"></i><span>Expenses</span></a>
                        </li>
                        <li><a href="/payroll" class="{{Request::is('payroll') ?'active':''}}"><i class="fa fa-envira"
                                                                                                  aria-hidden="true"></i><span>Payroll</span></a>
                        </li>
                        <li>
                            <a href="#subPages2" data-toggle="collapse"
                               class="{{substr(Request::path(),0,10)=='engagement'  ? 'active':'collapsed '}}"><i
                                        class="lnr lnr-briefcase"></i> <span>Engagements</span> <i
                                        class="icon-submenu lnr lnr-chevron-left"></i></a>
                            <div id="subPages2"
                                 class="collapse {{substr(Request::path(),0,10)=='engagement' ?'in':''}}">
                                <ul class="nav">
                                    <li><a href="{{route('engagement.index')}}"
                                           class="{{Request::is('engagement') ?'active':''}}">As member</a></li>
                                    <li><a href="{{route('engagement.create')}}"
                                           class="{{Request::is('engagement/create') ?'active':''}}">As Leader</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="/profile" class="{{Request::is('profile') ?'active':''}}"><i class="fa fa-id-badge"
                                                                                                  aria-hidden="true">&nbsp;</i><span>My Profile</span></a>
                        </li>
                        <li><a href="/message" class="{{Request::is('message') ?'active':''}}"><i
                                        class="fa fa-envelope-o" aria-hidden="true"></i><span>Messages</span></a></li>
                        @if(Auth::user()&&Auth::user()->isSupervisor())
                            <li>
                                <a href="#subPages3" data-toggle="collapse"
                                   class="{{str_contains(Request::path(),'admin') ? 'active':'collapsed '}}"><i
                                            class="lnr lnr-users"></i> <span>Administration</span> <i
                                            class="icon-submenu lnr lnr-chevron-left"></i></a>
                                <div id="subPages3" class="collapse {{str_contains(Request::path(),'admin') ?'in':''}}">
                                    <ul class="nav">
                                        <li><a href="/admin/hour" class="{{Request::is('admin/hour')?'active':''}}">Endorse
                                                Hours</a></li>
                                        <li><a href="/admin/expense"
                                               class="{{Request::is('admin/expense')?'active':''}}">Endorse Expenses</a>
                                        </li>
                                        <li><a href="/admin/engagement"
                                               class="{{Request::is('admin/engagement')?'active':''}}">Grant
                                                Engagements</a></li>
                                        <li><a href="/admin/user" class="{{Request::is('admin/user') ?'active':''}}">Users</a>
                                        </li>
                                        <li><a href="/admin/client"
                                               class="{{Request::is('admin/client') ?'active':''}}">Clients</a></li>
                                        <li><a href="/admin/miscellaneous"
                                               class="{{Request::is('admin/other') ?'active':''}}">Other Resources</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        <li><a href="/test" class="{{Request::is('test') ?'active':''}}"><i class="lnr lnr-code"></i>
                                <span>Dev Test</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- END LEFT SIDEBAR -->
        <!-- MAIN -->
        <div class="main">
            <!-- MAIN CONTENT -->
        @yield('content')
        <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN -->
        <div class="clearfix"></div>
        <footer>
            <div class="container-fluid">
                <p class="copyright">&copy; 2017 <a href="#" target="_blank">New Life CFO</a>. All Rights Reserved.</p>
            </div>
        </footer>
    </div>
    <!-- END WRAPPER -->
@endsection