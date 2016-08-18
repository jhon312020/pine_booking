@extends('layouts.app')

@section('content')

<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Yearly Report</h1>
                </div>
                <div class="col-lg-12">
                    <h4 class="text-center text-primary"> Overall expenses of the year {{ $year }} - Rs. {{ number_format($total_expense_of_year,2) }} </h4>
					<h4 class="text-center text-primary"> Overall incomes of the year {{ $year }} - Rs. {{ number_format($total_income_of_year,2) }} </h4>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Expenses chart for the year {{ $year }} 
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                        <span class="caret"></span>
                                    </button>
                                     <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="{{url($url[0])}}">{{ $url['link_name'] }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="bar-chart"></div>
                        </div>
                        <!-- /.panel-body -->
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
            Morris.Bar({
                element: 'bar-chart',
                data: {!! $expenses_grap_data !!},
                xkey: 'Month',
                ykeys: ['expense', 'income'],
				lineColors: ['#7a92a3', '#000'],
                labels: ['Expense', 'Income'],
                parseTime: false,
            });

        });
        </script>
@endsection
