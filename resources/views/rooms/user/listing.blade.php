@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <!-- <div class="col-lg-12">
            <h1 class="page-header"></h1>
        </div> -->
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Rooms List</h1>
            </div>
            <div class="pull-right" style="display:table; margin-top:20px;">
                <a href="{{url('/room/add')}}" class="btn btn-primary"></i> Add </a>
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
                        <div class="flash-message">
                            @if(Session::has('alert-danger'))
                                <p class="alert alert-danger"> One room deleted successfully</p>
                            @endif
							@if(Session::has('alert-success'))
                                <p class="alert alert-danger"> {{Session::get('alert-success')}}</p>
                            @endif
                        </div>
                    <div class="panel-body">
            <table class="table table-responsive table-striped task-table" id="room_table">

                <!-- Table Headings -->
                <thead>
                    <th>Room Number</th>
                    <th>&nbsp;</th>
                </thead>

                <!-- Table Body -->
                <tbody>

                    @if (count($rooms) > 0)
                        @foreach ($rooms as $room)
                            <tr>
                                <!-- room Name -->
                                <td class="table-text">
                                    <div>{{ $room->room_number }}</div>
                                </td>
                                <td>
                                <a href="{{url('/room/edit/'.$room->id)}}" class="btn btn-success" style="float:left; margin-right:10px;"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                    <form action="{{ url('room/delete/'.$room->id) }}" method="POST">
                                        {!! csrf_field() !!}
                                        {!! method_field('DELETE') !!}
                                        <button type="submit" class="btn btn-danger" name="delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
									@if($room->is_disabled == 0)
									<a href="{{url('/room/update/'.$room->id)}}" class="btn btn-primary" style="float:left; margin-right:10px;"><i class="fa fa-lock"></i> Disable</a>
									@elseif($room->is_disabled == 1)
									<a href="{{url('/room/update/'.$room->id)}}" class="btn btn-primary" style="float:left; margin-right:10px;"><i class="fa fa-unlock"></i> Enable</a>
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
                    <h4>Total Rooms: <strong> {{ count($rooms) }}</strong></h4>
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
                            columns: [ 0]
                        },
						title: 'Rooms'
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [ 0]
                        },
						title: 'Rooms'
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [ 0]
                        },
						title: 'Rooms'
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
