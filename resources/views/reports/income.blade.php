@extends('layouts.app')

@section('content')
<style>
.table tbody tr.datatabletd {
    cursor:pointer;
}
</style>
<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Income Report</h1>
                </div>
                <div class="pull-right" style="display:table; margin-top:25px;">
                    <form role="form" method="POST" action="{{ url('reports/income') }}">
                        {!! csrf_field() !!}
                        <div class="form-group col-lg-5 pull-right">
                            <div class='input-group date datetimepicker'>
                                <span class="input-group-addon">
                                    To
                                </span>
                                <input type="text" class="form-control" name="to_date" placeholder="dd-mm-yyyy" value="{{$to_date}}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                <button type="submit" class="btn btn-primary pull-right btn-l-margin"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                         <div class="form-group col-lg-5 pull-right">
                            <div class='input-group date datetimepicker'>
                                 <span class="input-group-addon">
                                    From
                                </span>
                                <input type="text" class="form-control" name="from_date" placeholder="dd-mm-yyyy" value="{{$from_date}}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-12">
                    <h4 class="text-center text-primary"> Overall incomes from {{$from_date}} to {{$to_date}} - Rs. {{ number_format($total_income,2) }} </h4>
                    <h4 class="text-center text-primary"> Overall expenses from {{$from_date}} to {{$to_date}}  - Rs. {{ number_format($total_expenses,2) }} </h4>
                    
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Income report from {{$from_date}} to {{$to_date}}
                            <div class="pull-right">
                                
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table class="table table-responsive table-striped task-table" id="income_table">
                                <!-- Table Headings -->
                                <thead>
                                    <th>Date</th>
                                    <th>Income</th>
                                    <th>Expense</th>
                                </thead>
                                <!-- Table Body -->
                                <tbody>
                                @if (count($incomes) > 0)
                                    @foreach ($incomes as $income)
                                        <tr class="datatabletd">
                                            <td class="table-text">{{Date('d-m-Y', strtotime($income->report_date))}}</td>
                                            <td class="table-text">{{$income->income_amount}}</td>
                                            <td class="table-text">{{$income->expense_amount}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
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
        
            $('.datetimepicker').datetimepicker({format: 'DD-MM-YYYY'});
            var table = $('#income_table').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                     {
                        extend: 'print',
                         exportOptions: {
                            columns: [ 0, 1, 2]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [ 0, 1, 2]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [ 0, 1, 2]
                        }
                    },
                    'colvis'
                ]
            });
        
            var old_date = '';
            $('table tbody').on('click', 'tr.datatabletd', function () {
                var data = table.row( this ).data();
                var ele = $(this);
                var ind_date = data[0];
                var date = data[0].split('-');
                var report_date = date[2]+'-'+date[1]+'-'+date[0];
                var formData = [{name:'report_date', value:report_date  }, {name:'_token',value:$('[name=_token]').val()}];
                $('.duplicateDiv').slideUp('slow', function(){
                    $('.duplicateRow').remove();
                });
                if(old_date != ind_date) {
                    $.ajax({
                        url:"{{action('ExpenseController@getDateIncome')}}",
                        type:"post",
                        data:formData,
                        dataType:'json',
                        success:function(data, textStatus, jqXHR) {
                            old_date = ind_date;
                            var html = 
                            '<tr class="duplicateRow" style="display:none;">'+
                                '<td colspan=3>'+
                                    '<div class="col-sm-12 duplicateDiv" style="display:none;">'+
										'<div class="col-sm-6">'+
                                            '<div class="panel panel-default">'+
                                                '<div class="panel-heading">'+
                                                    '<i class="fa fa-bar-chart-o fa-fw"></i> Incomes on '+ind_date+
                                                '</div>'+
                                                '<div class="panel-body">'+
                                                    '<table class="table">'+
                                                        '<thead>'+
                                                            '<tr>'+
                                                                '<th>Category</th>'+
                                                                //'<th>Date</th>'+
                                                                '<th>Type</th>'+
                                                                '<th>Amount</th>'+
                                                            '</tr>'+    
                                                        '</thead>'+
                                                        '<tbody>';
                                                            $.each(data['income_list'], function(i,v){
                                                                html += 
                                                                '<tr>'+
                                                                    '<td>'+(v.category?v.category:'Reservation')+'</td>'+
                                                                    //'<td>'+v.updated_at+'</td>'+
                                                                    '<td>'+(v.mode_of_payment?v.mode_of_payment:'Cash')+'</td>'+
                                                                    '<td>'+v.paid+'</td>'+
                                                                '</tr>';    
                                                            })
                                                        html +=     
                                                        '</tbody>'+
                                                    '</table>'+
                                                '</div>'+
                                            '</div>'+
                                                    
                                        '</div>'+
                                        '<div class="col-sm-6">'+
                                            '<div class="panel panel-default">'+
                                                '<div class="panel-heading">'+
                                                    '<i class="fa fa-bar-chart-o fa-fw"></i> Expenses on '+ind_date+
                                                '</div>'+
                                                '<div class="panel-body">'+
                                                    '<table class="table">'+
                                                        '<thead>'+
                                                            '<tr>'+
                                                                '<th>Category</th>'+
                                                                //'<th>Date</th>'+
                                                                '<th>Type</th>'+
                                                                '<th>Amount</th>'+
                                                            '</tr>'+    
                                                        '</thead>'+
                                                        '<tbody>';
                                                            $.each(data['expense_list'], function(i,v){
                                                                html += 
                                                                '<tr>'+
                                                                    '<td>'+v.category.charAt(0).toUpperCase() + v.category.slice(1)+'</td>'+
                                                                    //'<td>'+v.date_of_expense+'</td>'+
                                                                    '<td>'+v.name+'</td>'+
                                                                    '<td>'+v.amount+'</td>'+
                                                                '</tr>';    
                                                            })
                                                        html +=     
                                                        '</tbody>'+
                                                    '</table>'+
                                                '</div>'+
                                            '</div>'+       
                                        '</div>'+
                                        
                                    '</div>'+
                                '</td>'+
                            '</tr>';
                            ele.after(html);
                            $('.duplicateRow').slideDown('slow');
                            $('.duplicateDiv').slideDown('slow');
                        },
                        error:function(jqXHR, textStatus, errorThrown){
                            console.log(textStatus+'----'+errorThrown);
                        }
                    });
                } else {
                    old_date = '';
                }
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
