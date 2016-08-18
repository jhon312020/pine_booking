<div class="modal fade" id="confirm_complete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Confirm Check Out</h4>
      </div>
      <div class="modal-body">
          @if(date('Y-m-d') < date('Y-m-d', strtotime($reservation->checkout)))
            Please check the customer's check out date ({{date('d-m-Y', strtotime($reservation->checkout))}}).
          @endif
          <div id="checkout_validation"></div>
          <br>
            Are you sure to check out this page?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="complete">Yes</button>
        <button type="button" class="btn" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
