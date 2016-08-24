@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <!-- <div class="col-lg-12">
            <h1 class="page-header"></h1>
        </div> -->
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Employee Payments</h1>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row">
                <div class="col-lg-6">
                    <div class="flash-message">
                      @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">Payment added successfully</p>
                        @endif
                      @endforeach
                    </div>
                    <!-- Display Validation errors -->
                    @include('common.errors')
                    <!-- Validation Errors  -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Advance Payment
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ url('employees/pay/'.$employees[0]->id) }}">
                                {!! csrf_field() !!}
                                <div class="form-group input-group" id="datetimepicker1">
                                    <input type="text" class="form-control" name="updated_at" placeholder="Paying date : dd-mm-yyyy" id="paying_date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
								<div class="form-group">
									<select class="form-control" name="category" id="category">
										<option value="">Select Category</option>
										@foreach($category as $key => $value)
										<option value="{{$key}}">{{$value}}</option>
										@endforeach
									</select>
								</div>
                                <div class="form-group">
                                    <input type="text" class="form-control" data-validation="required" placeholder="Pay amount" name="paid" id="paid">
                                </div>
								<div class="form-group">
                                    <input type="text" class="form-control" placeholder="Notes" name="notes" id="notes">
                                </div>
                                <button type="reset" class="btn btn-danger pull-right btn-l-margin">Cancel</button>
                                <button type="submit" class="btn btn-primary pull-right">Add</button>
                            </form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <div class="col-lg-6">
                    <table class = "table table-bordered">
                        <tr>
                            <td>Employee Name </td>
                            <td><strong>{{$employees[0]->first_name}} {{$employees[0]->last_name}}</strong></td>
                        </tr>
                        <tr>
                            <td>Address </td>
                            <td>{{$employees[0]->address}}</td>
                        </tr>
                        <tr>
                            <td>Email </td>
                            <td>{{ $employees[0]->email }}</td>
                        </tr>
                        <tr>
                            <td>Phone </td>
                            <td>{{ $employees[0]->phone }}</td>
                        </tr>
                    </table>
                </div>
                <!-- /.col-lg-12 -->
            </div>

         <!-- /.row -->
         <div class="row">
             <div class="col-lg-6">
                        <form role="form" method="POST" action="{{ url('employees/pay/'.$employees[0]->id) }}">
                        {!! csrf_field() !!}
                        <div class="form-group pull-right">
                            <div class='input-group date datetimepicker' id="datetimepicker2">
                                <span class="input-group-addon">
                                    Select Month
                                </span>
                                <input type="text" class="form-control" name="advance_report_month" placeholder="mm-yyyy" value="{{Date('m/Y')}}" }}>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                <button type="submit" class="btn btn-primary pull-right btn-l-margin"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        </form>
             </div>
         </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel-body">
                <table class="table table-responsive table-striped task-table" id="advance_table">  
                    <!-- Table Headings -->
                    <thead>
                        <th>Date</th>
                        <th>Amount</th>
						<th>Category</th>
                    </thead>
                    <!-- Table Body -->
                    <tbody>
                        @if (count($employee_payments) > 0)
                            @foreach ($employee_payments as $payment)
								    <tr>
                                        <!-- room Name -->
                                        <td class="table-text">
                                            <div>{{ date('d-m-Y', strtotime($payment->updated_at)) }}</div>
                                        </td>
                                        <td class="table-text">
                                            <div>{{ number_format($payment->amount, 2) }}</div>
                                        </td>
										<td class="table-text">
                                            <div>{{ ucfirst($payment->category) }}</div>
                                        </td>
                                    </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    Total advance
                </div>
                <div class="panel-body">
                    <h3 class="text-center">Rs. {{ number_format($total_advance_paid,2)}} /-</h3>
                </div>
            </div>

        </div>
        
    </div>
</div>
</div>
</div>
@include('common.modal_confirm')
<!-- /#page-wrapper -->
@endsection
@section('load_js')
    @parent
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script src="{{asset('datepicker/moment-with-locales.js')}}"></script>
    <script src="{{asset('datepicker/datetimepicker.js')}}"></script>
    <script src="{{asset('datatables/js/jquery.dataTables.js')}}"></script>
    <script src="{{asset('datatables/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('datatables/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('datatables/js/jszip.min.js')}}"></script>
    <script src="{{asset('datatables/js/pdfmake.min.js')}}"></script>
    <script src="{{asset('datatables/js/vfs_fonts.js')}}"></script>
    <script src="{{asset('datatables/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('datatables/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('datatables/js/buttons.colVis.min.js')}}"></script>
    <script type="text/javascript">
        $.validate({
            lang: 'en'
        });
        $(function () {
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $('#datetimepicker1').datetimepicker({format: 'DD-MM-YYYY'});
            $('#datetimepicker2').datetimepicker({viewMode: 'months', format: 'MM/YYYY', defaultDate: new Date()});
            $("#paying_date").keypress(function(event) {event.preventDefault();});
            $('#paying_date').val(today.getDate()+'-'+(today.getMonth() + 1)+'-'+today.getFullYear());
            $('#advance_table').DataTable();
        });
        $('button[name="delete"]').on('click', function(e){
            e.preventDefault();
            var $form=$(this).closest('form'); 
            $('#confirm').modal({ backdrop: 'static', keyboard: false })
            .one('click', '#delete', function() {
                $form.trigger('submit'); // submit the form
            });
        // .one() is NOT a typo of .on()
        });
		$('#category').change(function(){
			if($(this).val() == 'salary') {
				var formData = [{name:'id', value:'{{$employees[0]->id}}'}, {name:'_token',value:$('[name=_token]').val()}, {name:'pay_date', value:$('#paying_date').val()}];
				var ele = $(this);
				$.ajax({
					url:"{{action('EmployeeController@getSalary')}}",
					type:"post",
					data:formData,
					dataType:'json',
					success:function(data, textStatus, jqXHR) {
						ele.closest('.form-group').find('.help-block').remove();
						if(data['balance']) {
							$('#paid').val(data['balance']);	
						} else {
							ele.after('<span class="help-block form-error">There is no pending payment for this employee</span>')		
						}
					},
					error:function(jqXHR, textStatus, errorThrown){
						console.log(textStatus+'----'+errorThrown);
					}
				});	
			} else {
				$('#paid').val('');
			}
		})
    </script>
@endsection
