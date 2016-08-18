@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <!-- <div class="col-lg-12">
            <h1 class="page-header"></h1>
        </div> -->
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Room Info</h1>
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
                    <div class="panel-body">
                    <table class="table table-responsive table-striped task-table" id="room_table">  
                <!-- Table Headings -->
                <thead>
                    <th>Name</th>
                    <th>Short Name</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>&nbsp;</th>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @if (count($room_types) > 0)
                        @foreach ($room_types as $room)
                            <tr>
                                <!-- room Name -->
                                <td class="table-text">
                                    <div>{{ $room->name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $room->short_name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ number_format($room->base_price, 2) }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $room->base_availability }}</div>
                                </td>
                                <!-- Delete Button -->
                                <td>
                                    <form action="{{ url('room_type/delete/'.$room->id) }}" method="POST">
                                        {!! csrf_field() !!}
                                        {!! method_field('DELETE') !!}

                                        <button type="submit" class="btn btn-danger" name="delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
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
                    <h4>Total Rooms: <strong> {{ count($room_types) }}</strong></h4>
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
            $('.datetimepicker').datetimepicker({format: 'DD-MM-YYYY'});
            $('#room_table').DataTable( {
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
