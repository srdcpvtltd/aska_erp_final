

<div class="row">
    <div class="col-lg-6 col-md-6 mb-4">
        <label for="name" class="form-label"> Role Name <span style="color: red">*</span> </label>
        <input type="text" class="form-control" id="name" required name="name" value="{{ ( isset($roleDetail) ? $roleDetail->name: '' )}}" autocomplete="off" placeholder="">
    </div>
    <div class="col-lg-6 col-md-6 mb-4">
        <label for="name" class="form-label"> Guard Name</label>
        <input type="text" class="form-control" id="guard_name" required name="guard_name" value="web" autocomplete="off" readonly>
    </div>
    <div class="col-lg-6 col-md-6 text-start mb-4 mt-md-4">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{isset($roleDetail)? 'Update':'Create'}} Role</button>
    </div>
</div>
