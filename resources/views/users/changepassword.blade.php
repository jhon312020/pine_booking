@extends('layouts.app')

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Change Password</h1>
        </div>
        <!-- /.col-lg-12 -->
         <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                    <!-- Display Validation errors -->
                    @include('common.errors')
                    @if (isset($message) && count($message) > 0)
                        <!-- Form Error List -->
                        <div class="alert {{ $message['class'] }}">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {!! $message['message']  !!}
                        </div>
                    @endif
                    <!-- Validation Errors  -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Password
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ url('user/changepassword') }}">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Old Password" name="old_password">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="New password">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm password">
                                </div>
                                <button type="reset" class="btn btn-danger pull-right btn-l-margin">Cancel</button>
                                <button type="submit" class="btn btn-primary pull-right">Change</button>
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
