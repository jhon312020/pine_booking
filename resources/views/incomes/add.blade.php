@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add Income</h1>
      <div class="pull-right" style="margin-top:20px;">
                <a href="{{url('/income')}}" class="btn btn-primary"></i> Income List </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- /.col-lg-12 -->
         <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                    <!-- Display Validation errors -->
                    @include('common.errors')
                    <!-- Validation Errors  -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Income
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ url('income/add') }}">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <!--<input type="text" class="form-control" placeholder="Income Name" name="name"> -->
                                    <select name="name" id="category" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach($income_category as $key=>$value)
                                              <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group input-group" id="datetimepicker1">
                                    <input type="text" class="form-control" name="date_of_income" placeholder="Date of Income : dd-mm-yyyy">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <div class="form-group input-group">
                                    <span class="input-group-addon">Rs.</span>
                                    <input type="number" class="form-control" name="amount">
                                    <span class="input-group-addon">.00</span>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="notes" placeholder="Notes"></textarea>
                                </div>
                                <button type="reset" class="btn btn-danger pull-right btn-l-margin">Cancel</button>
                                <button type="submit" class="btn btn-primary pull-right">Add</button>
                            </form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
               <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-body">
            <table class="table table-striped task-table" id="income_table">

                <!-- Table Headings -->
                <thead>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>&nbsp;</th>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @if (count($incomes) > 0)
                        @foreach ($incomes as $income)
                            <tr>
                                <!-- Expense Name -->
                                <td class="table-text">
                                    <div>{{ date('d-m-Y', strtotime($income->date_of_income)) }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $income->name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ number_format($income->amount, 2) }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $income->notes }}</div>
                                </td>
                                <!-- Delete Button -->
                                <td>
                  <form action="{{ url('income/delete/'.$income->id) }}" method="POST">
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


    </div>
</div>
<!-- /#page-wrapper -->
@include('common.modal_confirm')
@endsection
@section('load_js')
    @parent
    <script src="{{asset('datepicker/moment-with-locales.js')}}"></script>
    <script src="{{asset('datepicker/datetimepicker.js')}}"></script>
    <script type='text/javascript'>
        $(function() {
            $('#datetimepicker1').datetimepicker({format: 'DD-MM-YYYY'});
            $('#side-menu').metisMenu();
      
      $('button[name="delete"]').on('click', function(e){
        e.preventDefault();
        var $form=$(this).closest('form'); 
        $('#confirm').modal({ backdrop: 'static', keyboard: false })
        .one('click', '#delete', function() {
          $form.trigger('submit'); // submit the form
        });
      // .one() is NOT a typo of .on()
      });
        });
    </script>
@endsection
