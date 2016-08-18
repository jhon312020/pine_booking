<div class="modal fade" id="payment_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Payment</h4>
      </div>
       @include('common.errors')
                    <form role="form" method="POST" id="mypayform" action="{{ url('reservation/advance/'.$reservation->id) }}">
                        {!! csrf_field() !!}
      <div class="modal-body">
            <!-- Display Validation errors -->
           
                        <div class="form-group input-group" id="datetimepicker3">
                            <input type="text" class="form-control" name="updated_at" placeholder="Paying date : dd-mm-yyyy" id="paying_date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <div class="form-group">
                            <select name="mode_of_payment" class="form-control">
                                <option value="">Select Payment Mode</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="category" class="form-control">
                                <option value="">Select Category</option>
                                <option value="Rent">Rent</option>
                                <option value="Food">Food</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" data-validation="required" placeholder="Pay amount" name="paid">
                        </div>
                        <input type="hidden" class="form-control" name="checkmyform" value="mypayment">


      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Add</button>
                    
        <!--<button type="button" class="btn btn-primary" data-dismiss="modal" id="delete">Yes</button>-->
        <button type="button" class="btn" data-dismiss="modal" id="close_payment">No</button>
      </div>
      </form>
    </div>
  </div>
</div>
