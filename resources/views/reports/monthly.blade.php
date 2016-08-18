@extends('layouts.app')

@section('content')

<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Report</h1>
                </div>
                <div class="col-lg-12">
                    <h4 class="text-center text-primary"> Overall expenses for the month of {{ $month }} - Rs. {{ number_format($total_expense_of_month,2) }} </h4>
					<h4 class="text-center text-primary"> Overall incomes for the month of {{ $month }} - Rs. {{ number_format($total_income_of_month,2) }} </h4>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Expenses chart for the month of {{ $month }}
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        {{ $month }}
                                        <span class="caret"></span>
                                    </button>
                                     <ul class="dropdown-menu pull-right" role="menu">
                                         @for ($month = 1; $month <= $month_numeric; $month++)
                                            <li><a href="{{url('/reports/month', array('month'=>date('M', mktime(null, null, null, $month))))}}" active='active'>{{ date("M", mktime(null, null, null, $month)) }}</a>
                                            </li>
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="line-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <div class="col-lg-12">
                    <h4 class="text-center text-primary"> Overall cash in hand for the month - Rs. {{ number_format(($total_income_of_month - $total_expense_of_month), 2) }} </h4>
                    
                </div>
            </div>
            <!-- /.row -->
        </div>
<!-- /#page-wrapper -->

@endsection
@section('load_js')
    @parent
    <!-- Morris Charts JavaScript -->
        <script src="{{asset('bower_components/raphael/raphael-min.js')}}"></script>
        <script src="{{asset('bower_components/morrisjs/morris.min.js')}}"></script>
        <!-- <script src="{{asset('js/morris-data.js')}}"></script> -->
        <script type='text/javascript'>
        $(function() {
            Morris.Line({
                element: 'line-chart',
                data: {!! $expenses_grap_data !!},
                xkey: 'Day',
                ykeys: ['value', 'income'],
				lineColors: ['#7a92a3', '#000'],
                labels: ['Expense', 'Income'],
                parseTime: false,
            });

        });
        </script>
@endsection
