@extends('layouts.master')
@section('title')
    {{ __('Manage Purchase') }}
@endsection

@section('scripts')
    <script>
        $('.copy_link').click(function(e) {
            e.preventDefault();
            var copyText = $(this).attr('href');

            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.alertButton').forEach(function(a) {
            a.addEventListener('click', function(event) {
                const formId = 'delete-form-' + this.getAttribute('data-id');
                const form = document.getElementById(formId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

@section('main-content')
    <style>
        .action-btn {
            width: 29px;
            height: 28px;
            border-radius: 9.3552px;
            color: #fff;
            display: inline-table;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
    </style>
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Purchase') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.purchase.create', 0) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                title="{{ __('Create') }}">
                Add
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th> {{ __('Purchase') }}</th>
                                    <th> {{ __('Vendor') }}</th>
                                    <th> {{ __('Category') }}</th>
                                    <th> {{ __('Purchase Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit purchase') || Gate::check('delete purchase') || Gate::check('show purchase'))
                                        <th> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td class="Id">
                                            <a href="{{ route('admin.purchase.show', \Crypt::encrypt($purchase->id)) }}"
                                                class="btn btn-outline-primary">{{ Auth::user()->purchaseNumberFormat($purchase->purchase_id) }}</a>
                                        </td>
                                        <td> {{ !empty($purchase->vender) ? $purchase->vender->name : '' }} </td>
                                        <td>{{ !empty($purchase->category) ? $purchase->category->name : '' }}</td>
                                        <td>{{ Auth::user()->dateFormat($purchase->purchase_date) }}</td>
                                        <td>
                                            @if ($purchase->status == 0)
                                                <span
                                                    class="purchase_status badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 1)
                                                <span
                                                    class="purchase_status badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 2)
                                                <span
                                                    class="purchase_status badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 3)
                                                <span
                                                    class="purchase_status badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 4)
                                                <span
                                                    class="purchase_status badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit purchase') || Gate::check('delete purchase') || Gate::check('show purchase'))
                                            <td class="Action">
                                                <span>
                                                    <div class="action-btn bg-info">
                                                        <a href="{{ route('admin.purchase.show', \Crypt::encrypt($purchase->id)) }}"
                                                            class="mx-3 btn btn-sm align-items-center"
                                                            data-bs-toggle="tooltip" title="{{ __('Show') }}"
                                                            data-original-title="{{ __('Detail') }}">
                                                            <i class="link-icon" data-feather="eye"></i>
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-primary">
                                                        <a href="{{ route('admin.purchase.edit', \Crypt::encrypt($purchase->id)) }}"
                                                            class="mx-3 btn btn-sm align-items-center"
                                                            data-bs-toggle="tooltip" title="Edit"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-danger">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['admin.purchase.destroy', $purchase->id],
                                                            'class' => 'delete-form-btn delete_btn',
                                                            'id' => 'delete-form-' . $purchase->id,
                                                        ]) !!}
                                                        <a class="mx-3 btn btn-sm align-items-center bs-pass-para alertButton"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-id="{{ $purchase->id }}">
                                                            <i class="link-icon" data-feather="trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
