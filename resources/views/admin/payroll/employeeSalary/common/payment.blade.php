

<div class="modal fade" id="paymentForm" tabindex="-1" aria-labelledby="addslider" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title" id="exampleModalLabel">Make Payment</h5>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div id="modal-errors"></div>

                    <form class="forms-sample" id="payrollPayment" action=""  method="post" >
                        @csrf
                        @method('put')
                        <div class="row">
                            <label for="exampleFormControlSelect1" class="form-label">Payment Method<span style="color: red">*</span></label>
                            <div class="col-lg-12 mb-3">
                                <div class="col-lg-12 mb-3">
                                    <select name="payment_method_id" class="form-control" required>
                                        <option disabled selected>Select Payment Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method['id'] }}"> {{ $method['name'] }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <label for="paid_on" class="form-label">Payment Date<span style="color: red">*</span></label>
                            <div class="col-lg-12 mb-3">
                                <input type="date" class="form-control" id="paid_on" name="paid_on" value="" required />
                            </div>

                        </div>
                        <div class="text-center">
                            <button type="submit" id="submit-btn" class="btn btn-primary btn-xs"> submit </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>






