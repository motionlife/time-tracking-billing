@extends('layouts.html')
@section('wrapper')
    <div id="wrapper">
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
                                class="fa fa-calendar" aria-hidden="true"></i>&nbsp;<span>REPORT TIME</span></a>
                </div>
                <div id="navbar-menu">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="lnr lnr-question-circle"></i> <span>Help</span> <i
                                        class="icon-submenu lnr lnr-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="notification-item"><span class="dot bg-warning"></span>Basic Use</a>
                                </li>
                                <li><a href="#" class="notification-item"><span class="dot bg-danger"></span>Working
                                        With Data</a></li>
                                <li><a href="#" class="notification-item"><span
                                                class="dot bg-success"></span>Security</a></li>
                                <li><a href="#" class="notification-item"><span class="dot bg-info"></span>Troubleshooting</a>
                                </li>
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
                                <li><a href="/message"><i class="lnr lnr-envelope"></i> <span>Message</span></a></li>
                                <li><a href="#"><i class="lnr lnr-cog"></i> <span>Settings</span></a></li>
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
                                           class="{{Request::is('engagement') ?'active':''}}">My Engagements</a></li>
                                    <li><a href="{{route('engagement.create')}}"
                                           class="{{Request::is('engagement/create') ?'active':''}}">Led Engagements</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="#subPages3" data-toggle="collapse"
                               class="{{str_contains(Request::path(),'approval') ? 'active':'collapsed '}}"><i
                                        class="fa fa-gavel" aria-hidden="true"></i><span>Approval</span> <i
                                        class="icon-submenu lnr lnr-chevron-left"></i></a>
                            <div id="subPages3" class="collapse {{str_contains(Request::path(),'approval') ?'in':''}}">
                                <ul class="nav">
                                    <li><a href="/approval/hour?summary=1" class="{{Request::is('approval/hour') ?'active':''}}">Time
                                            Reports</a></li>
                                    <li><a href="/approval/expense?summary=1"
                                           class="{{Request::is('approval/expense') ?'active':''}}">Expense Reports</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="/profile" class="{{Request::is('profile') ?'active':''}}"><i class="fa fa-id-badge"
                                                                                                  aria-hidden="true">&nbsp;</i><span>My Profile</span></a>
                        </li>
                        @if(Auth::user()&&Auth::user()->isSupervisor())
                            <li>
                                <a href="#subPages4" data-toggle="collapse"
                                   class="{{str_contains(Request::path(),'admin') ? 'active':'collapsed '}}"><i
                                            class="lnr lnr-users"></i> <span>Administration</span> <i
                                            class="icon-submenu lnr lnr-chevron-left"></i></a>
                                <div id="subPages4" class="collapse {{str_contains(Request::path(),'admin') ?'in':''}}">
                                    <ul class="nav">
                                        <li><a href="/admin/report" class="{{Request::is('admin/report')||Request::is('admin/hour')||Request::is('admin/expense')?'active':''}}">Admin Reports</a></li>
                                        <li><a href="/admin/engagement"
                                               class="{{Request::is('admin/engagement')?'active':''}}">Grant
                                                Engagements</a></li>
                                        <li><a href="/admin/bp"
                                               class="{{Request::is('admin/bp')||Request::is('admin/bill')||Request::is('admin/payroll')?'active':''}}">Billing & Payroll</a>
                                        </li>
                                        <li><a href="/admin/user" class="{{Request::is('admin/user') ?'active':''}}">Users</a>
                                        </li>
                                        <li><a href="/admin/client"
                                               class="{{Request::is('admin/client') ?'active':''}}">Clients</a></li>
                                        <li><a href="/admin/miscellaneous"
                                               class="{{Request::is('admin/miscellaneous') ?'active':''}}">Miscellaneous</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        <div class="main">
            @yield('content')
        </div>
        <div class="clearfix"></div>
        <footer>
            <div class="container-fluid">
                <p class="copyright">&copy; 2018 <a href="#" target="_blank">New Life CFO</a>. All Rights Reserved.</p>
            </div>
        </footer>
    </div>
@endsection