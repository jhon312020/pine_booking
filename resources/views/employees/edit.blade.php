@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12 page-header" style="margin-top:20px;">
            <div class="pull-left">
                <h1>Employee</h1>
            </div>
            <div class="pull-right" style="display:table; margin-top:20px;">
                <a href="{{url('/employees/list')}}" class="btn btn-primary"></i> List </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- /.col-lg-12 -->
         <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                        <div class="flash-message">
                          @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                            <p class="alert alert-{{ $msg }}">Employee updated Successfully</p>
                            @endif
                          @endforeach
                        </div>
                    <!-- Display Validation errors -->
                    @include('common.errors')
                    <!-- Validation Errors  -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Edit Employee
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ url('employees/edit/'.$Employee->id) }}" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="First Name" name="first_name" value="{{ $Employee->first_name }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Last Name" name="last_name" value="{{ $Employee->last_name }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Address" name="address" value="{{ $Employee->address }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Phone" name="phone" value="{{ $Employee->phone }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Email" name="email" value="{{ $Employee->email }}">
                                </div>
								<div class="form-group">
                                    <input type="text" class="form-control" placeholder="Salary" name="salary" value="{{ $Employee->salary }}">
                                </div>
								<div class="form-group">
                                    <span class="btn btn-default btn-file" id="up_prf">
										Upload Proof <input type="file" class="form-control" placeholder="Upload image" name="image" id="image">
									</span>
                                </div>
								<div class="form-group">
									@if($Employee->image != '' && @is_array(getimagesize(asset('images/employee/'.$Employee->image))))
										<img id="customer_proof_img" width="200" src="{{asset('images/employee/'.$Employee->image)}}"></img>
									@else
										<a href="{{url('images/employee/'.$Employee->image)}}" target="_blank">{{$Employee->image}}</a>
									@endif
								</div>
                                <button type="reset" class="btn btn-danger pull-right btn-l-margin">Cancel</button>
                                <button type="submit" class="btn btn-primary pull-right">Update</button>
                            </form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

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
            $('#datetimepicker1').datetimepicker({format: 'DD-MM-YYYY'});
            $('#side-menu').metisMenu();
        });
    </script>
@endsection
