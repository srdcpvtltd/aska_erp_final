
@extends('layouts.master')

@section('title','QR')
@section('styles')
    <style>
        .qr > svg {
            height: 100px;
            width: 100px;
        }
    </style>
@endsection
@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.qr.index')}}">QR section</a></li>
                <li class="breadcrumb-item active" aria-current="page">QR</li>
            </ol>

            @can('create-qr')
                <a href="{{ route('admin.qr.create')}}">
                    <button class="btn btn-primary add_qr">
                        <i class="link-icon" data-feather="plus"></i>Add QR
                    </button>
                </a>
            @endcan
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>QR Image</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                        @forelse($qrData as $qr)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $qr->title }}</td>
                                <td class="qr_code">
                                    <div class="qr">{!! $qr->qr_code !!}</div>
                                </td>

                                <td class="text-center">
                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                        <li class="me-2">
                                            <a href="{{route('admin.qr.print',$qr->id)}}" target="_blank" class="text-success" title="Print QR">
                                                <i class="link-icon" data-feather="printer"></i>
                                            </a>
                                        </li>
                                        @can('edit-qr')
                                            <li class="me-2">
                                                <a href="{{route('admin.qr.edit',$qr->id)}}" class="text-warning" title="Edit QR ">
                                                    <i class="link-icon" data-feather="edit"></i>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('delete-qr')
                                            <li class="me-2">
                                                <a class="deleteQR"
                                                   data-href="{{route('admin.qr.destroy',$qr->id)}}" title="Delete QR">
                                                    <i class="link-icon"  data-feather="delete"></i>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
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

{{--        <div class="dataTables_paginate mt-3">--}}
{{--            {{$qr->appends($_GET)->links()}}--}}
{{--        </div>--}}



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


            $('.deleteQR').click(function (event) {
                event.preventDefault();
                let href = $(this).data('href');
                Swal.fire({
                    title: 'Are you sure you want to delete this data ?',
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

            //
            // window.print();
            // window.onfocus = function () {
            //     window.close();
            // }
        });

    </script>
@endsection






