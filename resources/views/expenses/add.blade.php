@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="pull-left">
                <h1>Expense</h1>
            </div>
            <div class="pull-right" style="display:table; margin-top:20px;">
                <a href="{{url('/expense/list')}}" class="btn btn-primary"></i> List </a>
            </div>
            <div class="clearfix"></div>
        </div>
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
                            Add Expense
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ url('expense/add') }}">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach($expense_category as $key=>$value)
                                              <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Expense Name" name="name" id="expense_name">
                                    <select name="name" class="form-control hide" id="food_category" disabled>
                                    @foreach($food_category as $key=>$value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group input-group" id="datetimepicker1">
                                    <input type="text" class="form-control" name="date_of_expense" placeholder="Date of Expense : dd-mm-yyyy">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-inr"></i></span>
                                    <input type="number" class="form-control" name="amount" min="1" id='amount'>
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
                            <th>Action</th>
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
                                        <td class="table-text">
                                            <a href="{{url('/expense/edit/'.$expense->id)}}" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                        </button>
                                        </td>
                                        <!-- Delete Button -->
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

    </div>
</div>
<!-- /#page-wrapper -->

@endsection
@section('load_js')
    @parent
    <script src="{{asset('datepicker/moment-with-locales.js')}}"></script>
    <script src="{{asset('datepicker/datetimepicker.js')}}"></script>
    <script type='text/javascript'>
        $(function() {
            $('#category').bind('change', function() {
                if($(this).val() == 'Food') {
                    $('#food_category').removeClass('hide').prop('disabled', false);
                    $('#expense_name').addClass('hide').prop('disabled', true);
                }
                else {
                    $('#food_category').addClass('hide').prop('disabled', true);
                    $('#expense_name').removeClass('hide').prop('disabled', false);
                }
                
            });
            $('#datetimepicker1').datetimepicker({format: 'DD-MM-YYYY', minDate: new Date('{{date("m/d/Y", strtotime("-2 days"))}}'), maxDate:new Date('{{date("m/d/Y", strtotime("+2 days"))}}')  });
            $("#amount").bind('keyup mouseup', function() {
                if(isNaN($(this).val()) || $(this).val() <= 0) {
                    $(this).val('');
                }
            });

            $('#side-menu').metisMenu();
        });
    </script>
@endsection
