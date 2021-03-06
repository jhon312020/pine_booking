@extends('layouts.app')

@section('content')
<style type="text/css">
.ui-autocomplete {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  float: left;
  display: none;
  min-width: 160px;
  _width: 160px;
  padding: 4px 0;
  margin: 2px 0 0 0;
  list-style: none;
  background-color: #ffffff;
  border-color: #ccc;
  border-color: rgba(0, 0, 0, 0.2);
  border-style: solid;
  border-width: 1px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
  -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  -webkit-background-clip: padding-box;
  -moz-background-clip: padding;
  background-clip: padding-box;
  *border-right-width: 2px;
  *border-bottom-width: 2px;
}
.ui-autocomplete .ui-menu-item, .ui-autocomplete .ui-menu-item > a.ui-corner-all {
  display: block;
  padding: 3px 15px;
  clear: both;
  font-weight: normal;
  line-height: 18px;
  color: #555555;
  white-space: nowrap;
}
.ui-autocomplete .ui-menu-item:hover, .ui-autocomplete .ui-menu-item > a.ui-corner-all.ui-state-hover, .ui-autocomplete .ui-menu-item > a.ui-corner-all.ui-state-active {
  color: #ffffff;
  text-decoration: none;
  background-color: #0088cc;
  border-radius: 0px;
  -webkit-border-radius: 0px;
  -moz-border-radius: 0px;
  background-image: none;
}
.mytot_sec .help-block {
    position: absolute !important;
    margin-left: 0px !important;
    margin-top: -2px !important;
    left: 0px !important;
}
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
</style>
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
        
        <!-- /.col-lg-12 -->
         <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="flash-message">
                      @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">Room booked successfully!</p>
                        @endif
                      @endforeach
                    </div>
                    <!-- Display Validation errors -->
                    @include('common.errors')
                    <!-- Validation Errors  -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Book A Room
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ url('reservation/add') }}" enctype="multipart/form-data" autocomplete="false">
                                {!! csrf_field() !!}
                                <div class="col-lg-6">
                                    <div class="form-group input-group" id="datetimepicker1">
                                        <input type="text" class="form-control" name="checkin" placeholder="Check In Date : dd-mm-yyyy" id="checkin" value="{{$today}}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <div class="form-group input-group" id="datetimepicker2">
                                        <input type="text" class="form-control" id="checkout" name="checkout" placeholder="Check Out Date : dd-mm-yyyy" value="{{$tomorrow}}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <span class="label label-danger">Total available rooms : <span id="avl_rms">{{ (count($total_available_rooms) - $queries) }}</span></span>
                                        <input type="number" class="form-control" placeholder="Booking Rooms" name="booked_rooms" data-validation="required" id="booked_rooms" min='1' autocomplete="false" value="">
                                    </div>
                                    <!-- <div class="form-group">
                                        <select name="room_type_id" class="form-control" id="room_type_id">
                                            <option>Select available rooms</option>                
                                        </select> 
                                    </div>-->
                                    <div class="form-group">
                                        <input type="number" class="form-control" placeholder="One Room Price Per Day" name="rent" data-validation="required" id="target">
                                    </div>
                                    <div class="form-group input-group mytot_sec">
                                        <span class="input-group-addon"><i class="fa fa-inr"></i></span>
                                        <input type="number" class="form-control" placeholder="Total Price" name="total_price" id="my_total_price" readonly="true">
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                    <div class="form-group input-group">
                                        <span class="input-group-addon"><i class="fa fa-inr"></i></span>
                                        <input type="number" class="form-control" placeholder="Advance" name="advance">
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" name="reference" placeholder="Reference"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" id="phone" data-validation="required" class="form-control" placeholder="Phone" name="phone">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" data-validation="required" placeholder="First Name" name="first_name" id="first_name">
                                    </div>    
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Last Name" name="last_name" id="last_name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"  placeholder="Email" name="email" id="email">
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" name="address" placeholder="Address" id="address"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <span class="btn btn-default btn-file" id="up_prf">
                                            Upload Proof <input type="file" class="form-control" placeholder="Upload image" name="image" id="image">
                                        </span>
                                        <!-- <input type="file" class="form-control" placeholder="Upload image" name="image" id="image">-->                                        
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                          <label><input type="checkbox" name="is_active" value="1"> Confirm</label>
                                        </div>
                                    </div>                                  
                                    <div class="form-group">
                                    <img id="customer_proof_img" width="200">
                                    </div>
                                    
                                </div>
                                <div class="clearfix"></div>
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

    </div>
</div>
<!-- /#page-wrapper -->
@endsection
@section('load_js')
    @parent
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <script src="{{asset('datepicker/moment-with-locales.js')}}"></script>
    <script src="{{asset('datepicker/datetimepicker.js')}}"></script>
    <script type='text/javascript'>
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
        function calculate_total_price() {
            var a = $('#datetimepicker1').data("DateTimePicker").date();
            var b = $('#datetimepicker2').data("DateTimePicker").date();
            var timeDiff = 0
            if (b) {
                timeDiff = (b - a) / 1000;
            }
            var DateDiff = Math.floor(timeDiff / (60 * 60 * 24));
            total_price = DateDiff * $('#target').val() * $('#booked_rooms').val();
            $('#my_total_price').val(total_price);
        }
        $(function() {
            $("#checkin").keypress(function(event) {event.preventDefault();});
            $("#checkout").keypress(function(event) {event.preventDefault();});
            $("#phone").keypress(function(event) {
                $('#image').show();
                $('#customer_proof_img').hide();
                $('#view-proof').hide();
                $('#up_prf').show();
                $('#first_name').val("");
                $('#last_name').val("");
                $('#email').val("");
                $('#address').val("");
            });
            // Datetimepicker has issues on setting the minDate https://github.com/Eonasdan/bootstrap-datetimepicker/issues/1302
            $('#datetimepicker1').datetimepicker({format: 'DD-MM-YYYY', minDate: moment().startOf('day').add(-2, 'd').millisecond(0).second(0).minute(0).hour(0) });
            $('#datetimepicker2').datetimepicker({format: 'DD-MM-YYYY', minDate:new  Date('{{date("Y-m-d", strtotime("+1 day"))}}')});
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
            $('#side-menu').metisMenu();
        });
        
        $(function()
        {
            //var base_url = 'http://localhost/expenses/public/index.php';
            //var base_url = 'http://stage.cygnusinfosystems.com/pine_booking/public';
            //base_url + "/reservation/show_customer_detail"
            function showdata( message ) {
                if(message!="Nothing") {
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    var dataString = 'id='+message+'_token='+CSRF_TOKEN;
                    $.ajax({
                        type: "POST",
                        url : "{{action('ReservationController@show_customer_detail')}}" ,
                        data : dataString,
                        success : function(data){
                            $('#first_name').val(data[0]['first_name']);
                            $('#last_name').val(data[0]['last_name']);
                            $('#email').val(data[0]['email']);
                            $('#address').val(data[0]['address']);
                            if ('image' in data[0]){
                                $('#customer_proof_img').attr('src', data[0]['image']);
                                $('#image').hide();
                                $('#customer_proof_img').show();
                                $('#up_prf').hide();
                            }
                            else {
                                var link = "<a id='view-proof' href='"+data[0]['link']+"' target='_blank'>View Proof</a>";
                                $('#up_prf').after(link);
                                $('#image').hide();
                                $('#up_prf').hide();
                            }
                        }
                    },"html");
                }
            }
            $("#phone").autocomplete({
              source: "autocomplete",
              minLength: 3,
              select: function(event, ui) {
                $('#phone').val(ui.item.value);
                showdata(ui.item ? ui.item.id:"Nothing");
              }
            });

            $('#image').change(function () {

                var f = this.files[0]
                if (f.size > 2097152 || f.fileSize > 2097152)
                {
                   alert("Allowed file size exceeded. (Max. 2 MB)")
                   this.value = null;
                }
            });
            calculate_total_price();
            $( "#target" ).blur(function() {
                calculate_total_price();
            });
            $( "#booked_rooms" ).blur(function() {
                calculate_total_price();
            });
            //check_available_rooms($('#checkin').val(), $('#checkout').val());
            $("#booked_rooms").bind('keyup mouseup', function() {
                if(isNaN($(this).val()) || $(this).val() <= 0) {
                    $(this).val('');
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ajaxStart(function(){
            $("#myspinner").css("display", "block");
        });

        $(document).ajaxComplete(function(){
            $("#myspinner").css("display", "none");
        });
    </script>
@endsection
