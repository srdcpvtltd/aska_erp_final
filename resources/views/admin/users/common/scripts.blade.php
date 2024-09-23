
<script src="{{asset('assets/vendors/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('assets/js/tinymce.js')}}"></script>

<script src="{{ asset('assets/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/jquery-validation/additional-methods.min.js') }}"></script>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.changePassword').click(function (event) {
            event.preventDefault();
            let url = $(this).data('href');
            $('.modal-title').html('User Change Password');
            $('#changePassword').attr('action',url)
            $('#statusUpdate').modal('show');
        });

        $('.toggleStatus').change(function (event) {
            event.preventDefault();
            let status = $(this).prop('checked') == true ? 1 : 0;
            let href = $(this).attr('href');
            // let toggleElement = $(this);

            // swal({
            //         title: "Are you sure you want to change Status ?",
            //         type: "warning",
            //
            //         confirmButtonColor: "#7066e0",
            //
            //         confirmButtonText: "Yes",
            //         showCancelButton: true,
            //         cancelButtonText: "No",
            //         closeOnConfirm: true,
            //         closeOnCancel: true
            //     },
            //     function(isConfirm){
            //         if (isConfirm) {
            //             window.location.href = href;
            //         } else {
            //             (status === 0)? toggleElement.prop('checked', true) :  toggleElement.prop('checked', false)
            //         }
            //     });

            Swal.fire({
                title: 'Are you sure you want to change Status ?',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                padding:'10px 50px 10px 50px',
                // width:'500px',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }else if (result.isDenied) {
                    (status === 0)? $(this).prop('checked', true) :  $(this).prop('checked', false)
                }
            })
        })

        $('.deleteEmployee').click(function (event) {
            event.preventDefault();
            let href = $(this).data('href');
            Swal.fire({
                title: 'Are you sure you want to Delete Employee Records ?',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                padding:'10px 50px 10px 50px',
                // width:'1000px',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            })
        })

        $('.forceLogOut').click(function (event) {
            event.preventDefault();
            let href = $(this).data('href');
            Swal.fire({
                title: 'Are you sure you want to force log out this employee ?',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                padding:'10px 50px 10px 50px',
                // width:'1000px',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            })
        })

        $('.changeWorkPlace').click(function (event) {
            event.preventDefault();
            let href = $(this).data('href');
            Swal.fire({
                title: 'Are you sure you want to Change Work Space?',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                padding:'10px 50px 10px 50px',
                // width:'1000px',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            })
        })

        $('#branch').change(function() {
            let selectedBranchId = $('#branch option:selected').val();
            let departmentId = "{{ $userDetail->department_id ?? $filterParameters['department_id'] ?? old('department_id') }}";
            $('#department').empty();
            $('#posts').empty();
            if (selectedBranchId) {
                $.ajax({
                    type: 'GET',
                    url: "{{ url('admin/departments/get-All-Departments') }}" + '/' + selectedBranchId ,
                }).done(function(response) {
                    if(!departmentId){
                        $('#department').append('<option disabled  selected >Select Department</option>');
                    }
                    response.data.forEach(function(data) {
                        $('#department').append('<option ' + ((data.id === departmentId) ? "selected" : '') + ' value="'+data.id+'" >'+capitalize(data.dept_name)+'</option>');
                    });
                    departmentChange();
                });
            }
        }).trigger('change');

        $('#department').change(function() {
            departmentChange();
        }).trigger('change');

        $('#post').change(function() {
            let selectedbranchId = $('#branch option:selected').val();
            let supervisorId = "{{ isset($userDetail)   ? $userDetail['supervisor_id'] : old('supervisor_id') }}";
            let officeTimeId = "{{ isset($userDetail)   ? $userDetail['office_time_id'] : old('office_time_id') }}";
            $('#supervisor').empty();
            $('#officeTime').empty();
            if (selectedbranchId) {
                $.ajax({
                    type: 'GET',
                    url: "{{ url('admin/users/get-company-employee') }}" + '/' + selectedbranchId ,
                }).done(function(response) {

                    console.log(response);
                    if(!supervisorId){
                        $('#supervisor').append('<option value=""  selected >--Select Supervisor--</option>');
                    }
                    response.employee.forEach(function(data) {
                        $('#supervisor').append('<option ' + ((data.id === supervisorId) ? "selected" : '') + '  value="'+data.id+'">' + capitalize(data.name) + '</option>');
                    });

                    if(!officeTimeId){
                        $('#officeTime').append('<option value="" selected >--Select Office Time--</option>');
                    }
                    response.officeTime.forEach(function(data) {
                        $('#officeTime').append('<option ' + ((data.id === officeTimeId) ? "selected" : '') + '  value="'+data.id+'" >'+(data.opening_time)+' - '+(data.closing_time) + '</option>');
                     });
                });
            }
        }).trigger('change')

    });

    function departmentChange() {
        let selectedDepartmentId = $('#department option:selected').val();
        let postId = "{{ isset($userDetail)  ? $userDetail['post_id'] : old('post_id') }}";
        $('#post').empty();
        if (selectedDepartmentId) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/posts/get-All-posts') }}" + '/' + selectedDepartmentId ,
            }).done(function(response) {
                if(!postId){
                    $('#post').append('<option value="" selected >--Select An Option--</option>');
                }
                response.data.forEach(function(data) {
                    $('#post').append('<option ' + ((data.id == postId) ? "selected" : '') + ' value="'+ data.id+'">' + capitalize(data.post_name) + '</option>');
                });
            });
        }
    }

    function capitalize(str) {
        strVal = '';
        str = str.split(' ');
        for (let chr = 0; chr < str.length; chr++) {
            strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
        }
        return strVal
    }

    $('#employeeDetail').validate({
            rules: {
                name: { required: true },
                address: { required: true },
                email: { required: true },
                role_id: { required: true },
                username: { required: true },
                phone: { required: true },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                address: {
                    required: "Please enter address"
                },
                email: {
                    required: "Please enter valid email"
                },

                role_id: {
                    required: "Please select role"
                },

                username: {
                    required: "Please enter username"
                },

                Phone: {
                    required: "Please enter phone number"
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('div').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
                $(element).removeClass('is-valid');
                $(element).siblings().addClass("text-danger").removeClass("text-success");
                $(element).siblings().find('span .input-group-text').addClass("bg-danger ").removeClass("bg-success");
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).addClass('is-valid');
                $(element).siblings().addClass("text-success").removeClass("text-danger");
                $(element).find('span .input-group-prepend').addClass("bg-success").removeClass("bg-danger");
                $(element).siblings().find('span .input-group-text').addClass("bg-success").removeClass("bg-danger ");
            }
        });

    $('#avatar').change(function(){
        const input = document.getElementById('avatar');
        const preview = document.getElementById('image-preview');
        const file = input.files[0];
        const reader = new FileReader();
        reader.addEventListener('load', function() {
            preview.src = reader.result;
        });
        reader.readAsDataURL(file);
        $('#image-preview').removeClass('d-none')

    })


    // document.addEventListener('DOMContentLoaded', function() {
    //     const leaveForm = document.getElementById('employeeDetail');
    //     const leaveAllocatedInput = document.getElementById('leave_allocated');
    //     const leaveDaysInputs = document.querySelectorAll('.leave-days');
    //     const errorMessage = document.getElementById('error-message');
    //     const oldValues = {};
    //
    //     // Initialize old values
    //     leaveDaysInputs.forEach(input => {
    //         oldValues[input.id] = input.value;
    //     });
    //
    //     function highlightError(element) {
    //         element.classList.add('is-invalid');
    //         element.nextElementSibling.style.display = 'block';
    //         element.nextElementSibling.style.color = 'red';
    //     }
    //
    //     function unHighlightError(element) {
    //         element.classList.remove('is-invalid');
    //         element.nextElementSibling.style.display = 'none';
    //     }
    //
    //     function validateForm(event) {
    //         let totalDays = 0;
    //         let isValid = true;
    //
    //         leaveDaysInputs.forEach(input => {
    //             totalDays += parseInt(input.value) || 0;
    //         });
    //
    //         if (parseInt(leaveAllocatedInput.value) < totalDays) {
    //             errorMessage.style.display = 'block';
    //             errorMessage.style.color = 'red';
    //             errorMessage.textContent = 'Allocated leave cannot be less than the total leave days.';
    //             leaveAllocatedInput.classList.add('is-invalid');
    //             isValid = false;
    //         } else {
    //             errorMessage.style.display = 'none';
    //             leaveAllocatedInput.classList.remove('is-invalid');
    //         }
    //
    //         leaveDaysInputs.forEach(input => {
    //             if (!input.value) {
    //                 highlightError(input);
    //                 isValid = false;
    //             } else {
    //                 unHighlightError(input);
    //             }
    //         });
    //
    //         if (!isValid) {
    //             event.preventDefault();
    //         }
    //     }
    //
    //     function setRequiredAttribute() {
    //         const leaveAllocatedValue = parseInt(leaveAllocatedInput.value);
    //         leaveDaysInputs.forEach(input => {
    //             if (leaveAllocatedValue > 0) {
    //                 if (!input.value) {
    //                     highlightError(input);
    //                 } else {
    //                     unHighlightError(input);  // Ensure errors are cleared once input is filled
    //                 }
    //             } else {
    //                 unHighlightError(input);
    //             }
    //         });
    //     }
    //
    //     leaveAllocatedInput.addEventListener('input', function() {
    //         setRequiredAttribute();
    //     });
    //
    //     leaveForm.addEventListener('submit', validateForm);
    //
    //     setRequiredAttribute();
    // });
    //
    // document.addEventListener('DOMContentLoaded', function() {
    //     const leaveDaysInputs = document.querySelectorAll('.leave-days');
    //     const isActiveCheckboxes = document.querySelectorAll('.is-active-checkbox');
    //
    //     leaveDaysInputs.forEach((input, index) => {
    //         input.addEventListener('input', function() {
    //             const isActiveCheckbox = isActiveCheckboxes[index];
    //             if (input.value === '') {
    //                 isActiveCheckbox.checked = false;
    //             } else {
    //                 input.classList.remove('is-invalid');  // Clear error when input is filled
    //                 input.nextElementSibling.style.display = 'none';  // Ensure error message is hidden
    //             }
    //         });
    //     });
    // });
    //

    document.addEventListener('DOMContentLoaded', function() {
        const leaveForm = document.getElementById('employeeDetail');
        const leaveAllocatedInput = document.getElementById('leave_allocated');
        const leaveDaysInputs = document.querySelectorAll('.leave-days');
        const errorMessage = document.getElementById('error-message');

        function displayError(element, message) {
            element.classList.add('text-danger');
            element.textContent = message;
            element.style.display = 'block';
        }

        function hideError(element) {
            element.classList.remove('text-danger');
            element.style.display = 'none';
        }

        function validateForm(event) {
            let totalDays = 0;
            let isValid = true;

            leaveDaysInputs.forEach(input => {
                totalDays += parseInt(input.value) || 0;
            });

            if (parseInt(leaveAllocatedInput.value) < totalDays) {
                displayError(errorMessage, 'Allocated leave cannot be less than the total leave days.');
                leaveAllocatedInput.classList.add('text-danger');
                isValid = false;
            } else {
                hideError(errorMessage);
                leaveAllocatedInput.classList.remove('text-danger');
            }

            leaveDaysInputs.forEach(input => {
                if (!input.value && parseInt(leaveAllocatedInput.value) > 0) {
                    displayError(input.nextElementSibling, 'This field is required.');
                    isValid = false;
                } else {
                    hideError(input.nextElementSibling);
                }
            });

            if (!isValid) {
                event.preventDefault();
            }
        }

        function setRequiredAttribute() {
            const leaveAllocatedValue = parseInt(leaveAllocatedInput.value);
            leaveDaysInputs.forEach(input => {
                if (leaveAllocatedValue > 0 && !input.value) {
                    displayError(input.nextElementSibling, 'This field is required.');
                } else {
                    hideError(input.nextElementSibling);
                }
            });
        }

        leaveAllocatedInput.addEventListener('input', function() {
            setRequiredAttribute();
        });

        leaveForm.addEventListener('submit', validateForm);

        setRequiredAttribute();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const leaveDaysInputs = document.querySelectorAll('.leave-days');
        const isActiveCheckboxes = document.querySelectorAll('.is-active-checkbox');

        leaveDaysInputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                const isActiveCheckbox = isActiveCheckboxes[index];
                if (input.value === '') {
                    isActiveCheckbox.checked = false;
                } else {
                    input.classList.remove('text-danger');  // Clear error when input is filled
                    input.nextElementSibling.style.display = 'none';  // Ensure error message is hidden
                }
            });
        });
    });




</script>
