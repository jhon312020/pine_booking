@extends('layouts.app')

@section('content')
<style type="text/css">
   #room_table th:nth-of-type(even) { 
        background-color: #eee;
    }
</style>

<div height="35" tabindex="-1" class="react-grid-Cell react-grid-Cell--locked" style="position:absolute;width:60px;height:35px;left:0;contain:layout;">
	<div class="react-grid-Cell__value">
		<span>
			<div class="react-grid-checkbox-container">
				<input class="react-grid-checkbox" type="checkbox" name="checkbox0">
				<label for="checkbox0" class="react-grid-checkbox-label"></label>
			</div>
		</span>
		<span> </span>
		<span> </span>
	</div>
</div>


<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12 page-header" style="margin-top:20px;">
                    <div class="pull-left">
                        <h2>Rooms Availability</h2>
                    </div>
                    <form method="post" id="dateForm" class="hide">
                        {!! csrf_field() !!}
                        <input type="hidden" name="from_date" id="from_date" value="{{date('01-m-Y')}}">
                        <input type="hidden" name="to_date" id="to_date" value="{{date('d-m-Y')}}">
                    </form>
                    <div class="pull-right col-lg-6" style="display:table; margin-top:25px;">
                        <form role="form" method="POST" action="{{ url('home/index') }}">
                        {!! csrf_field() !!}
                        <div class="form-group pull-right">
                            <div class='input-group date datetimepicker'>
                                <span class="input-group-addon">
                                    From
                                </span>
                                <input type="text" class="form-control" name="room_availability_from" placeholder="dd-mm-yyyy" value={{date('d-m-Y', strtotime($room_availability_from))}}>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                <button type="submit" class="btn btn-primary pull-right btn-l-margin"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <?php $flag=0; ?>
            <div class="row">
                <div class="col-lg-12">
                
                <h3>Total Rooms : {{count($rooms)}} </h3>
                <table class="table table-responsive table-striped table-bordered task-table" id="room_table">
                    <!-- Table Headings -->
                    <thead>
                        <th>Dates : </th>
                        @for($i=0; $i<$number_of_days; $i++) 
                            <th style='text-align:center;'>{{ date( "d", strtotime("$room_availability_from +$i day" )) }}<br>
                            <small>{{ date( "M-y", strtotime("$room_availability_from +$i day" )) }}</small> </th>
                        @endfor
                    </thead>
                    <tbody>
                        <tr>
                        <td><strong>Available Rooms : </strong></td>
                            @for($i=0; $i<$number_of_days; $i++)
                                <?php $bookedroom = 0; ?>
                                @if(isset($orderbydate[date( "Y-m-d", strtotime("$room_availability_from +$i day"))]))
                                    <?php $bookedroom = $orderbydate[date("Y-m-d", strtotime("$room_availability_from +$i day"))]; ?>
                                @endif
                                <?php $availableRooms = ((count($rooms) - $bookedroom) < 0)?0:count($rooms) - $bookedroom; ?>
                                @if($availableRooms)
                                    <?php $available_room_style = 'text-align:center;color:white;background-color:green;'; ?>
                                @else
                                    <?php $available_room_style = 'text-align:center;color:white;background-color:red;'; ?>
                                @endif
                                <td style=' <?php echo $available_room_style; ?>'>
                                <strong>
                                    {{ $availableRooms }}
                                </strong>
                                </td>

                            <!--td style='text-align:center;color: #337AB7'>
                            <strong>
                                @foreach($orderbydate as $key => $bookedroom)
                                    @if(date( "d-m-Y", strtotime("$room_availability_from +$i day" )) == date( "d-m-Y", strtotime("$key" )))
                                     {{ ((count($rooms) - $bookedroom) < 0)?0:count($rooms) - $bookedroom }}
                                     <?php $flag=1; ?>
                                    @endif
                                @endforeach
                                @if($flag==1) 
                                     <?php $flag=0; ?>
                                @else
                                    {{ count($rooms) }}
                                @endif
                            </strong>
                            </td-->
                        @endfor
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <h2>Income and Expense info</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-hand-o-right fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"># {{ $income_count }}</div>
                                    <div>Total no. of incomes</div>
                                </div>
                            </div>
                        </div>
                         <a href="javascript:void(0);" class="datailHref" data-action="{{action('IncomeController@listing')}}">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa  fa-rupee fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $total_income_of_month }}</div>
                                    <div>Total incomes</div>
                                </div>
                            </div>
                        </div>
                         <a href="javascript:void(0);" class="datailHref" data-action="{{action('IncomeController@listing')}}">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
								<div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-hand-o-right fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"># {{ $expense_count }}</div>
                                    <div>Total no. of expenses</div>
                                </div>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="datailHref" data-action="{{URL::to('expense/list')}}">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                           
            </div>
						<div class="row">
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-green">
										<div class="panel-heading">
												<div class="row">
														<div class="col-xs-3">
																<i class="fa fa-rupee fa-5x"></i>
														</div>
														<div class="col-xs-9 text-right">
																<div class="huge">{{ $total_expense_of_month }}</div>
																<div>Total expenes</div>
														</div>
												</div>
										</div>
										 <a href="javascript:void(0);" class="datailHref" data-action="{{URL::to('expense/list')}}">
												<div class="panel-footer">
														<span class="pull-left">View Details</span>
														<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
														<div class="clearfix"></div>
												</div>
										</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-green">
										<div class="panel-heading">
												<div class="row">
														<div class="col-xs-3">
																<i class="fa fa-rupee fa-5x"></i>
														</div>
														<div class="col-xs-9 text-right">
																<div class="huge">{{ $cash_in_hand }}</div>
																<div>Cash in hand</div>
														</div>
												</div>
										</div>
										 <a href="javascript:void(0);" class="">
												<div class="panel-footer">
														<span class="pull-left">View Details</span>
														<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
														<div class="clearfix"></div>
												</div>
										</a>
								</div>
							</div>
						</div>
            @if($role == 'admin')
            <div class="row">
               <!-- <div class="col-lg-12">
                    <h4 class="text-center text-primary"> Overall expenses for the month of {{Date('F')}} - Rs. {{ number_format($total_expense_of_month,2) }} </h4>
                    <h4 class="text-center text-primary"> Overall incomes of the month of {{Date('F')}} - Rs. {{ number_format($total_income_of_month,2) }} </h4>
                </div>-->
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Income and Expense chart for the month of {{ date('F') }}
                        </div>
                        
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="line-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
            </div>
            @endif;
            <!-- /.row -->
        </div>
<!-- /#page-wrapper -->

@endsection
@section('load_js')
    @parent
    <!-- Morris Charts JavaScript -->
        <script src="{{asset('bower_components/raphael/raphael-min.js')}}"></script>
        <script src="{{asset('bower_components/morrisjs/morris.min.js')}}"></script>
        <script src="{{asset('datepicker/moment-with-locales.js')}}"></script>
        <script src="{{asset('datepicker/datetimepicker.js')}}"></script>
        <!-- <script src="{{asset('js/morris-data.js')}}"></script> -->
        @if($role == 'admin')
        <script type='text/javascript'>
        $(function() {
            Morris.Line({
                element: 'line-chart',
                data: {!! $expenses_grap_data !!},
                xkey: 'Day',
                ykeys: ['income', 'value' ],
                labels: ['Income', 'Expense'],
                lineColors: ['#008000', '#CC3333'],
                parseTime: false,
            });
        });
        </script>
        @endif;
        <script type='text/javascript'>
        $(function() {
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $('.datetimepicker').datetimepicker({format: 'DD-MM-YYYY',minDate:today});
            $('.datailHref').bind('click', function(){
                $('#dateForm').attr('action', $(this).data('action')).submit();
                
            })
        });
        </script>
@endsection
