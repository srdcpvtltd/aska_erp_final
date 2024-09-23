@extends('layouts.master')
@section('title','App Setting')
@section('main-content')

    <section class="content">
        @include('admin.section.flash_message')
        <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Feature Control</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($features as $feature)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong> {{ ucfirst($feature->name) }} </strong></td>
                                <td><strong> {{ ucfirst($feature->group) }} </strong></td>

                                <td>
                                    <label class="switch">
                                        <input class="toggleStatus"
                                               href="{{route('admin.feature.toggle-status',$feature->id)}}"
                                               type="checkbox" {{($feature->status) == 1 ?'checked':''}}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-center"><b>No records found!</b></p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addslider" tabindex="-1" aria-labelledby="addslider" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title" id="exampleModalLabel">Export Table Data</h5>
                    </div>
                    <div class="modal-body">
                        <a href="{{route('admin.leave-type-export')}}">
                            <button class="btn btn-secondary btn-sm">Leave Types </button>
                        </a>
                        <a href="{{route('admin.leave-request-export')}}">
                            <button class="btn btn-success btn-sm">Leave Requests </button>
                        </a>
                        <a href="{{route('admin.employee-lists-export')}}">
                            <button class="btn btn-warning btn-sm">Employee Lists </button>
                        </a>
                        <a href="{{route('admin.attendance-lists-export')}}">
                            <button class="btn btn-danger btn-sm">Attendances  </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.toggleStatus').change(function (event) {
                event.preventDefault();
                var status = $(this).prop('checked') === true ? 1 : 0;
                var href = $(this).attr('href');
                Swal.fire({
                    title: 'Are you sure you want to change status ?',
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


        });
    </script>
@endsection




