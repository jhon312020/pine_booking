@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <!-- <div class="col-lg-12">
            <h1 class="page-header"></h1>
        </div> -->
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Expenses</h1>
            </div>
            <div class="pull-right" style="display:table; margin-top:25px;">
                <form role="form" method="POST" action="{{ url('food/list') }}">
                {!! csrf_field() !!}
                <div class="form-group col-lg-3 pull-right">
                    <div class='input-group date datetimepicker'>
                        <span class="input-group-addon">
                            To
                        </span>
                        <input type="text" class="form-control" name="date_of_expense_to" placeholder="dd-mm-yyyy" value="{{ $expense_date_to }}">
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
                        <input type="text" class="form-control" name="date_of_expense_from" placeholder="dd-mm-yyyy" value="{{ $expense_date_from }}">
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
                    <div class="panel-body">
            <table class="table table-responsive table-striped task-table" id="expense_table">

                <!-- Table Headings -->
                <thead>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>&nbsp;</th>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @if (count($expenses) > 0)
                        @foreach ($expenses as $expense)
                            <tr>
                                <!-- Expense Name -->
                                <td class="table-text">
                                    <div>{{ $expense->category }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ date('d-m-Y', strtotime($expense->date_of_expense)) }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $expense->name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ number_format($expense->amount, 2) }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $expense->notes }}</div>
                                </td>
                                <!-- Delete Button -->
                                <td>
                                    <form action="{{ url('expense/delete/'.$expense->id) }}" method="POST">
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
                    <h4>Total Expenses: <strong><i class="fa fa-inr"></i> {{ $total_expenses }}</strong></h4>
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
            $('#expense_table').DataTable( {
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    {"orderable": false },
                ],
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
