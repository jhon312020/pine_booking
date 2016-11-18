<?php
    //echo Route::currentRouteaction();
    $url = Route::getCurrentRoute()->getactionName();
    $controller = str_replace('App\\Http\\Controllers\\', '', $url);
    $controller = strtolower(str_replace('Controller', '', $controller));
    list($controller, $action) = explode('@', $controller);
    $actions = array('add', 'edit', 'pay', 'list', 'index', 'list');
?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a class="{{ ($controller == 'home')?'active':'' }}" href="{{url('/home')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li>
                <a class="{{ ($controller && $controller == 'reservation' && ((isset($action)) && (in_array($action, $actions) || $action == 'advance')))?'active':'' }}" href="{{url('/reservation/index')}}"><i class="fa fa-list fa-fw"></i> Booking</a>
            </li>
            <li>
                <a class="{{ ($controller && ($controller == 'expense' || $controller == 'others') && ((isset($action)) && in_array($action, $actions)))?'active':'' }}" href="{{url('/expense/list')}}"><i class="fa fa-list fa-fw"></i> Expenses</a>
            </li>
            <li>
                <a class="{{ ($controller && $controller == 'customer' && ((isset($action)) && in_array($action, $actions)))?'active':'' }}" href="{{url('/customer/list')}}"><i class="fa fa-list fa-fw"></i> Customers</span></a>
            </li>
            <li>
                <a  href="{{url('income')}}"><i class="fa fa-list fa-fw"></i> Incomes</span></a>
            </li>
            <li>
                <a class="{{ ($controller && $controller == 'room' && ((isset($action)) && in_array($action, $actions)))?'active':'' }}" href="{{url('/room/list')}}"><i class="fa fa-list fa-fw"></i> Rooms</a>
            </li> 
            <li>
                <a class="{{ ($controller && $controller == 'employees' && ((isset($action)) && in_array($action, $actions)))?'active':'' }}" href="{{url('/employees/list')}}"><i class="fa fa-list fa-fw"></i> Employees</a>
            </li> 
            <li>
                <a href="#"><i class="fa fa-sitemap fa-fw"></i> Reports<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level {{ Request::is('reports/month/*') || Request::is('reports/currentyear') || Request::is('reports/lastyear')?'collapse in':'' }}" >
                    @if(Auth::user()->role == 'admin')
                    <li>
                        <a class="{{ Request::is('reports/month/*')?'active':'' }}" href="{{url('/reports/monthly')}}">Current Month</a>
                    </li>
                    <li>
                        <a class="{{ Request::is('reports/currentyear') || Request::is('reports/lastyear') ?'active':'' }}" href="{{url('/reports/currentyear')}}">Current Year</a>
                    </li>
                    @endif
                    <li>
                        <a href="{{url('/reports/income')}}">Income/Expense</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
