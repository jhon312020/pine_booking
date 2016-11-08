@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Bookings</h1>
            </div>
            <div class="pull-right" style="margin-top:20px;">
                <a href="{{url('/reservation/index')}}" class="btn btn-primary"></i> List </a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row" id="completed_status">
        <div class="col-lg-12">
                    <div class="flash-message">
                        @if(Session::has('alert-success'))
                            <p class="alert alert-success">Room booking updated successfully!</p>
                        @endif
                        @if(Session::has('alert-info'))
                            <p class="alert alert-success">Payment added successfully</p>
                        @endif
                    </div>
                    <!-- Display Validation errors -->
                    @include('common.errors')
                    <!-- Validation Errors  -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Room Booking
                        </div>
                        <div class="panel-body">
                                <div class="col-lg-6">
                                <form role="form" method="POST" action="{{url('reservation/advance/'.$reservation->id)}} ">
                                {!! csrf_field() !!}
                                <label for="checkin">Check In:</label>
                                    <div class="form-group input-group" id="datetimepicker1">
                                        
                                        <input type="text" class="form-control" name="checkin" placeholder="Check In Date : dd-mm-yyyy" id="checkin" value="{{ date('d-m-Y', strtotime($reservation->checkin)) }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <label for="checkout">Check Out:</label>
                                    <div class="form-group input-group" id="datetimepicker2">
                                        
                                        <input type="text" class="form-control" id="checkout" name="checkout" placeholder="Check Out Date : dd-mm-yyyy" value="{{ date('d-m-Y', strtotime($reservation->checkout)) }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label for="booked_rooms">Booked Room <span class="label label-danger">Total available rooms : <span id="avl_rms"></span></span></label> 
                                        <input type="number" class="form-control" placeholder="Booking Rooms" name="booked_rooms" data-validation="required" id="booked_rooms" value="{{ $reservation->booked_rooms }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="rent">Room Price Per Day:</label>
                                        <input type="number" class="form-control" placeholder="One Room Price Per Day" name="rent" data-validation="required" id="target" value="{{ $reservation->rent }}">
                                    </div>
                                    <label for="total">Total Price</label>
                                    <div class="form-group input-group mytot_sec">
                                        <span class="input-group-addon"><i class="fa fa-inr"></i></span>
                                        <input type="number" class="form-control" placeholder="Total Price" name="total_price" id="my_total_price" readonly="true">
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                    <input type="hidden" class="form-control" placeholder="Advance" name="advance" value="{{ $reservation->advance }}">
                                    <input type="hidden" class="form-control" name="checkmyform" value="mybooking">
                                    <div class="form-group">
                                    <label for="reference">Reference</label>
                                        <textarea class="form-control" name="reference" placeholder="Reference">{{$reservation->reference}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" id="phone" data-validation="required" class="form-control" placeholder="Phone" name="phone" value="{{$reservation->customer->phone}}">
                                    </div>
                                    <div class="clearfix"></div>
                                    @if($reservation->cancel != 1) 
                                    <button type="reset" class="btn btn-danger pull-right btn-l-margin">Cancel</button>
                                    <button type="submit" class="btn btn-primary pull-right">Update</button>
                                    @endif
                                </form>
                                </div>
                                <div class="col-lg-6">
                                    
                                    <label for="customer">Customer Info:</label>
                                    <table class = "table table-bordered">
                                        <tr>
                                            <td>Customer Name </td>
                                            <td><strong>{{$reservation->customer->first_name}} {{$reservation->customer->last_name}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Booked Rooms </td>
                                            <td>{{$reservation->booked_rooms}}</td>
                                        </tr>
                                        <tr>
                                            <td>Check in date </td>
                                            <td>{{ date('d-m-Y', strtotime($reservation->checkin)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Check out date </td>
                                            <td>{{ date('d-m-Y', strtotime($reservation->checkout)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Phone </td>
                                            <td>{{ $reservation->customer->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>Proof</td>
                                            <td>
                                                @if (in_array(pathinfo(url('images/'.$reservation->customer->image), PATHINFO_EXTENSION), array('png','jpg','jpeg','gif','bmp','tiff')))
                                                    <img src="{{url('images/customers/'.$reservation->customer->image)}}" width="100">
                                                @else
                                                    <a href="{{url('images/customers/'.$reservation->customer->image)}}" target='_blank'>View Proof</a>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>                    
                            @if($reservation->cancel != 1) 
                            <button class="btn btn-primary btn-lg pull-right" name="payment" style="margin-left:10px;" id="jsPaymentButton">
                                <i class="fa fa-inr"></i> Payment
                            </button>
                            @endif
                                    
                                </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="col-sm-6 bg-success">
                <h4>
                    Booking Charges : <i class="fa fa-inr"></i></span> <strong id="total_rent"> </strong>
                </h4>
            </div>
            <div class="col-sm-6 bg-success">
                <h4>
                    Food and Other Charges : <i class="fa fa-inr"></i></span> <strong>
                        <?php $sum = 0; ?>
                        @foreach ($reservation_advances as $advance)
                            @if($advance->category == 'Advance' || $advance->category == 'Rent') 
                            @else   
                            <?php $sum += $advance->paid; ?>
                            @endif
                      
                        @endforeach
                        {{$sum}}
                    </strong>
                </h4>
            </div>
        </div>
        <div class="col-lg-12 ">
            <div class="col-sm-4 bg-warning">
                <h4>
                    Total Amount : <i class="fa fa-inr"></i></span> <strong id="total_sum"></strong>
                </h4>
            </div>
            <div class="col-sm-4 bg-warning">
                <h4>
                    Paid Amount : <i class="fa fa-inr"></i></span> <strong>{{$total_paid}}</strong>
                </h4>
            </div>
            <div class="col-sm-4 bg-warning">
                <h4>
                    Balance Amount: <i class="fa fa-inr"></i></span> <strong id="total_bal"></strong>
                </h4>
            </div>
        </div>
    </div>
    @if($reservation->cancel != 1) 
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-12 bg-success" style="padding:20px;">
                @if($reservation->completed == 0) 
                    @if(date('Y-m-d') < date('Y-m-d', strtotime($reservation->checkin)) )
                        <form action="{{ url('reservation/delete/'.$reservation->id) }}" method="POST">
                            {!! csrf_field() !!}
                            {!! method_field('DELETE') !!}
                            <button type="submit" class="btn btn-danger btn-lg pull-right" name="delete" style="margin-left:10px;">
                                <i class="fa fa-trash"></i> Cancel 
                            </button>
                        </form>
                    @endif
                    @if($reservation->is_active == 0)
                        <a href="{{url('/reservation/confirm/'.$reservation->id)}}" class="btn btn-warning btn-lg pull-right" style="float:left; margin-right:10px;"><i class="fa fa-pencil-square-o"></i> Confirm</a>
                    @else
                        <form action="{{ url('reservation/completed/'.$reservation->id) }}" method="POST">
                            {!! csrf_field() !!}
                            <button type="submit" class="btn btn-lg btn-warning pull-right" name="complete">
                                 Check Out
                            </button>
                        </form>
                       <!-- <a href="{{url('/reservation/completed/'.$reservation->id)}}" class="btn btn-lg btn-warning pull-right">Check Out</a> -->
                    @endif
                @else
                <div class="btn btn-lg btn-success pull-right">Completed</div>
                @endif
           
            </div>
        </div>
    </div>
    @endif
        <!-- /.col-lg-12 -->
         <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-body">
                    <table class="table table-responsive table-striped task-table" id="room_table">  
                <!-- Table Headings -->
                <thead>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Payment Mode</th>
                    <th>Paid</th>
                </thead>
                <!-- Table Body -->
                <tbody>
                    @if (count($reservation_advances) > 0)
                        @foreach ($reservation_advances as $advance)
                            <tr>
                                <!-- room Name -->
                                <td class="table-text">
                                    <div>{{ date('d-m-Y', strtotime($advance->updated_at)) }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $advance->category }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $advance->mode_of_payment }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ number_format($advance->paid, 2) }}</div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
    

    </div>
    </div>
</div>
<?php for( $i=1; $i<= 28; $i++){
                $mydates[]= date( "d-M-y", strtotime(date("Y-m-d", strtotime("+1 day")) . "-$i day"));
            } 
        ?>
@include('common.modal_confirm_booking')
@include('common.modal_booking_payment')
@include('common.modal_confirm_checkout')
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
         function check_available_rooms( checkin, checkout ) {
            if(checkin!="") {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var dataString = 'checkin='+checkin+'& checkout='+checkout+'& _token='+CSRF_TOKEN;
                $.ajax({
                    type: "POST",
                    url : "{{action('ReservationController@check_available_rooms')}}",
                    data : dataString,
                    success : function(data){
                        if({{count($total_available_rooms)}} > data) {
                            $('#avl_rms').text({{count($total_available_rooms)}} - data );
                        } else {
                            $('#avl_rms').text("0");    
                        }
                        
                    }
                },"html");
            }
        }
         check_available_rooms($('#checkin').val(), $('#checkout').val());
        $(function () {
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $('#datetimepicker3').datetimepicker({format: 'DD-MM-YYYY'});
            $("#paying_date").keypress(function(event) {event.preventDefault();});
            $('#paying_date').val(today.getDate()+'-'+(today.getMonth() + 1)+'-'+today.getFullYear());
/*            $('#room_table').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                     {
                        extend: 'print',
                         exportOptions: {
                            columns: [ 0, 1, 2,3 ]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [ 0, 1, 2,3 ]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [ 0, 1, 2,3 ]
                        }
                    },
                    'colvis'
                ]
            });*/
        });
        $('button[name="payment"]').on('click', function(e){
            e.preventDefault();
            var $form=$(this).closest('form'); 
            $('#payment_confirm').modal({ backdrop: 'static', keyboard: false })
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
        $('button[name="complete"]').on('click', function(e){
            e.preventDefault();
            var $form=$(this).closest('form'); 
            $('#confirm_complete').modal({ backdrop: 'static', keyboard: false })
            .one('click', '#complete', function() {
                $form.trigger('submit'); // submit the form
            });
        // .one() is NOT a typo of .on()
        });
        $('#close_payment').on('click', function(e){
            e.preventDefault();
            $("#mypayform").trigger('reset');
        });
            $("#checkin").keypress(function(event) {event.preventDefault();});
            $("#checkout").keypress(function(event) {event.preventDefault();});
            var date = new Date();
            var today = new Date(<?php echo date('Y'); ?>, <?php echo date('m'); ?>, <?php echo date('d'); ?>);
            var date2 = new Date(<?php echo date('Y'); ?>, <?php echo date('m'); ?>, <?php echo date('d'); ?>);
            var tomorrow = new Date(date2.getFullYear(), date2.getMonth(), date2.getDate()+1);
            var disabledat = "";
            function calculate_total_price() {
                var a = $('#datetimepicker1').data("DateTimePicker").date();
                var b = $('#datetimepicker2').data("DateTimePicker").date();
                var timeDiff = 0
                if (b) {
                    timeDiff = (b - a) / 1000;
                }
                var DateDiff = Math.floor(timeDiff / (60 * 60 * 24));
                total_price = DateDiff * $('#target').val() * $('#booked_rooms').val();
                total_sum = total_price + {{$sum}};
                total_balance = total_sum - {{$total_paid}};
                if (total_balance){
                    $("#checkout_validation").html('Payment pending is <i class="fa fa-inr"></i>'+total_balance);
                }
                $('#my_total_price').val(total_price);
                $('#total_rent').html(total_price);
                $('#total_sum').html(total_sum);
                $('#total_bal').html(total_balance);
            }
            
            $('#datetimepicker1').datetimepicker({format: 'DD-MM-YYYY' });
            $('#datetimepicker2').datetimepicker({format: 'DD-MM-YYYY',minDate:new  Date('{{date("Y-m-d 00:00:00", strtotime($minDateTo ))}}')});

            $("#datetimepicker1").on("dp.change", function (e) {
                var newdate = new Date(e.date);
                newdate.setDate(newdate.getDate() + 1);
                $('#datetimepicker2').data("DateTimePicker").minDate(newdate);
                calculate_total_price();
                check_available_rooms($('#checkin').val(), $('#checkout').val());
            });

            $('#datetimepicker2').on("dp.change", function(e){
                calculate_total_price();
                check_available_rooms($('#checkin').val(), $('#checkout').val());
            });
            calculate_total_price();
            $( "#target" ).blur(function() {
                calculate_total_price();
            });
            $( "#booked_rooms" ).blur(function() {
                calculate_total_price();
            });
    </script>
    @if($reservation->completed == 1)
    <script type="text/javascript">
        $("#completed_status :input").prop("disabled", true);
        $("#jsPaymentButton").prop("disabled", false);
    </script>
    @endif
    @if(date('Y-m-d', strtotime($reservation->checkin)) > date('Y-m-d'))
    @else
    <script type="text/javascript">
        $("#datetimepicker1 :input").prop("disabled", true);
    </script>
    @endif

<script type="text/javascript">
    $(document).ajaxStart(function(){
    $("#myspinner").css("display", "block");
});

$(document).ajaxComplete(function(){
    $("#myspinner").css("display", "none");
});
</script>

@endsection
