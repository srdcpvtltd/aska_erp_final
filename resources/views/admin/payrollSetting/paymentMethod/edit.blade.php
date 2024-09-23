
<div class="modal fade" id="addslider" tabindex="-1" aria-labelledby="addslider" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title" id="exampleModalLabel">Update Payment Method</h5>
            </div>
            <div class="modal-body">
                <div id="showErrorResponse d-none">
                    <div class="alert alert-danger errorPaymentMethod ">
                        <p class="errorMessage"></p>
                    </div>
                </div>

                <form class="forms-sample" id="updateForm" action="" method="post">
                    @method('PUT')
                  @csrf
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label for="name" class="form-label"> Payment Method Name <span style="color: red">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="payment_method"
                                   name="name"
                                   required
                                   value=""
                                  >
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary submit">Update</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>





