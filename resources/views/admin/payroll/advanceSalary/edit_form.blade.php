

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <form class="forms-sample" id="updateAdvanceSalaryRequestStatus"
                  enctype="multipart/form-data"
                  action="{{route('admin.advance-salaries.update',$advanceSalaryDetail->id)}}"
                  method="post" >
                @method('PUT')
                @csrf
                <div class="row">

                    <div class="col-lg-6 mb-3">
                        <label for="status" class="form-label"> Status <span style="color: red">*</span></label>
                        <select class="form-select form-select-lg" name="status" id="status" required>
                            <option value="" >Select Status</option>
                            <option value="processing" {{ $advanceSalaryDetail->status == 'processing' ? 'selected': ''}}>Processing</option>
                            <option value="approved" {{ $advanceSalaryDetail->status == 'approved' ? 'selected': ''}}>Approved</option>
                            <option value="rejected" {{ $advanceSalaryDetail->status == 'rejected' ? 'selected': ''}}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-lg-6 mb-3 releaseAmount">
                        <label for="released_amount" class="form-label"> Released Amount <span style="color: red">*</span></label>
                        <input type="number" min="100" class="form-control amountReleased"  name="released_amount"
                               value="{{  old('released_amount') }}"
                               autocomplete="off" placeholder="Enter Total Released Amount">
                    </div>

                    <div class="col-lg-6 mb-3 reason">
                        <label for="remark" class="form-label"> Remark <span style="color: red">*</span></label>
                        <textarea class="form-control remark"  name="remark" id="remark"  rows="4">{{old('remark')}}</textarea>
                    </div>

                    <div class="mb-3 col-12 document">
                        <h6 class="mb-2"> Attachments</h6>
                        <div>
                            <input id="image-uploadify" type="file"  class="attachment"  name="documents[]"
                                   accept=".pdf,.jpg,.jpeg,.png,.docx,.doc,.xls,.txt,.zip"  multiple />
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="link-icon" data-feather="edit-2"></i>Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
