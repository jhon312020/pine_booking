<?php
    $url = url()->current();
    $split = explode('/public/', $url);
    $action = array('add', 'edit');
    $controller = false;
    if(isset($split[1])) {
        $controller = true;
        $split1 = explode('/', $split[1]);
    }
    
?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a class="{{ (!$controller)?'active':'' }}" href="{{url('/home')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li>
                <a class="{{ ($controller && $split1[0] == 'reservation' && ((isset($split1[1])) && (in_array($split1[1], $action) || $split1[1] == 'advance')))?'active':'' }}" href="{{url('/reservation/index')}}"><i class="fa fa-list fa-fw"></i> Booking</a>
            </li>
            <li>
                <a class="{{ ($controller && ($split1[0] == 'expense' || $split1[0] == 'others') && ((isset($split1[1])) && in_array($split1[1], $action)))?'active':'' }}" href="{{url('/expense/list')}}"><i class="fa fa-list fa-fw"></i> Expenses</a>
            </li>
            <li>
                <a class="{{ ($controller && $split1[0] == 'customer' && ((isset($split1[1])) && in_array($split1[1], $action)))?'active':'' }}" href="{{url('/customer/list')}}"><i class="fa fa-list fa-fw"></i> Customers</span></a>
            </li>
            <li>
                <a  href="{{url('income')}}"><i class="fa fa-list fa-fw"></i> Incomes</span></a>
            </li>
            <li>
                <a class="{{ ($controller && $split1[0] == 'room' && ((isset($split1[1])) && in_array($split1[1], $action)))?'active':'' }}" href="{{url('/room/list')}}"><i class="fa fa-list fa-fw"></i> Rooms</a>
            </li> 
            <li>
                <a class="{{ ($controller && $split1[0] == 'employees' && ((isset($split1[1])) && in_array($split1[1], $action)))?'active':'' }}" href="{{url('/employees/list')}}"><i class="fa fa-list fa-fw"></i> Employees</a>
            </li> 
            <li>
                <a href="#"><i class="fa fa-sitemap fa-fw"></i> Reports<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level" >
                    <li>
                        <a href="{{url('/reports/monthly')}}">Current Month</a>
                    </li>
                    <li>
                        <a href="{{url('/reports/currentyear')}}">Current Year</a>
                    </li>
                    <li>
                        <a href="{{url('/reports/income')}}">Income/Expense</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
