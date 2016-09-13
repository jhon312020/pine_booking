@extends('layouts.app')

@section('content')
<style type="text/css">
    .clickable-row {
        cursor: pointer;    
    }    
</style>
<div id="page-wrapper">
    <div class="row">
        <!-- <div class="col-lg-12">
            <h1 class="page-header"></h1>
        </div> -->
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Booking List</h1>
            </div>
            <div class="pull-right" style="display:table; margin-top:25px;">
                <form role="form" method="POST">
                {!! csrf_field() !!}
                <div class="form-group col-lg-3 pull-right">
                    <div class='input-group date datetimepicker'>
                        <span class="input-group-addon">
                            To
                        </span>
                        <input type="text" class="form-control" name="to_date" placeholder="dd-mm-yyyy" value="{{ $reservation_date_to }}">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <button type="submit" class="btn btn-primary pull-right btn-l-margin"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                 <div class="form-group col-lg-3 pull-right">
                    <div class='input-group date datetimepicker'>
                         <span class="input-group-addon">
                            From
                        </span>
                        <input type="text" class="form-control" name="from_date" placeholder="dd-mm-yyyy" value="{{ $reservation_date_from }}">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                </form>
            </div>            
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            
        </div>
    </div>
        <!-- /.col-lg-12 -->
         <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                <div class="pull-right" style="margin-right:15px;"><a href="{{url('/reservation/add')}}" class="btn btn-primary"></i> Add </a></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="flash-message">
                        @if(Session::has('flash_message_cancel'))
                        <p class="alert alert-danger">Booking cancelled successfully!</p>
                        @endif
                        @if(Session::has('flash_message_confirmed'))
                        <p class="alert alert-success">Booking confirmed successfully!</p>
                        @endif
                        @if(Session::has('flash_message_completed'))
                        <p class="alert alert-success">Check out completed successfully!</p>
                        @endif
                    </div>
                    <div class="panel-body">
                    <table class="table table-responsive table-striped task-table table-hover" id="room_table">  
                <!-- Table Headings -->
                <thead>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Price</th>
                    <th>Advance</th>
                    <th>Booked</th>
                    <th>Check In </th>
                    <th>Check Out</th>
                    <th>Reference</th>
                    <th>&nbsp;</th>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @if (count($reservations) > 0)
                        @foreach ($reservations as $reservation)
                            @if ($reservation->cancel == 1)
                                <tr style="background-color:#F2DEDE !important;">
                            @elseif ($reservation->completed == 1)
                                <tr style="background-color:#DFF0D8 !important;">
                            @else
                                <tr>
                            @endif
                                <!-- reservation Name -->
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ $reservation->customer->first_name." ".$reservation->customer->last_name }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ $reservation->customer->phone }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ number_format($reservation->total_price, 2) }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ number_format($reservation->advance, 2) }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ $reservation->booked_rooms }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ date('d-m-Y', strtotime($reservation->checkin)) }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ date('d-m-Y', strtotime($reservation->checkout)) }}</div>
                                </td>
                                <td class="table-text clickable-row" data-href="{{ url('reservation/advance/'.$reservation->id) }}">
                                    <div>{{ $reservation->reference }}</div>
                                </td>
                                <!-- Delete Button -->
                                <td width="200">
                                @if($reservation->cancel == 0)
                                    @if($reservation->completed == 0) 
                                        @if(date('Y-m-d') < date('Y-m-d', strtotime($reservation->checkin)) )
                                            <form action="{{ url('reservation/delete/'.$reservation->id) }}" method="POST" style='display:inline;'>
                                                {!! csrf_field() !!}
                                                {!! method_field('DELETE') !!}
                                                <button type="submit" class="btn btn-danger" name="delete" style="margin-left:10px;">
                                                    <i class="fa fa-trash"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                        @if($reservation->is_active == 0)
                                            <a href="{{url('/reservation/confirm/'.$reservation->id)}}" class="btn btn-warning"><i class="fa fa-pencil-square-o"></i> Confirm</a>
                                        @else
                                            <a href="{{url('/reservation/advance/'.$reservation->id)}}" class="btn btn-warning">Check Out</a>
                                        @endif
                                    @else
                                    <div class="btn btn-success">Completed</div>
                                    @endif
                                @else
                                    <div class="btn btn-danger">Cancelled</div>
                                @endif
                           
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
    <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">
                    <h4>Total Booking: <strong> {{ count($reservations) }}</strong></h4>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>
@include('common.modal_confirm_booking')
<!-- /#page-wrapper -->
@endsection
@section('load_js')
    @parent
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
        $(function () {
            $(".clickable-row").click(function() {
                window.document.location = $(this).data("href");
            });
            $('.datetimepicker').datetimepicker({format: 'DD-MM-YYYY'});
            $('#room_table').DataTable( {
                dom: 'Bfrtip',
                "ordering": false,
                buttons: [
                     {
                        extend: 'print',
                         exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        },
                        title: 'Booking List'
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        },
                        title: 'Booking List'
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        },
                        title: 'Booking List'
                    },
                    'colvis'
                ]
            });
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
    </script>
@endsection
