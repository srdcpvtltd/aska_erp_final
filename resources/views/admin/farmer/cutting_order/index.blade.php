@extends('layouts.master')
@section('title')
    {{ __('Issue Cutting Order') }}
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#select-all').on('click', function() {
                $('.farming-detail').attr('checked', true);
                $('.farming-detail').val(1);
                $('#select-all').attr('hidden', true);
                $('#unselect-all').attr('hidden', false);
            });
            $('#unselect-all').on('click', function() {
                $('.farming-detail').attr('checked', false);
                $('.farming-detail').val(0);
                $('#select-all').attr('hidden', false);
                $('#unselect-all').attr('hidden', true);
            });
            $('#issue-cutting-order').on('click', function() {
                $('#issue-cutting-order').attr('disabled', true);
                $('#issue-cutting-order').html('Please Wait!!');
                var formData = {};
                $('.farming-detail').each(function() {
                    if ($(this).is(':checked')) {
                        formData[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0;
                    }
                });
                $.ajax({
                    url: "{{ route('admin.farmer.farming.update_cutting_order') }}",
                    method: 'post',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            });
        });
    </script>
@endsection

@section('button')
    <div class="float-end">
        @can('create-issue_cutting_order')
        <button type="button" class="btn btn-sm btn-info" id="select-all">
            Select All
        </button>
        <button type="button" class="btn btn-sm btn-danger" hidden id="unselect-all">
            Un-select All
        </button>
        <button type="button" class="btn btn-sm btn-success" id="issue-cutting-order">
            Issue Cutting Order
        </button>
        @endcan

    </div>
@endsection

@section('main-content')
    @include('admin.section.flash_message')

    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Issue Cutting Order') }}</li>
        </ol>

        <div class="float-end">
            @can('create-issue_cutting_order')
            <button type="button" class="btn btn-sm btn-info" id="select-all">
                Select All
            </button>
            <button type="button" class="btn btn-sm btn-danger" hidden id="unselect-all">
                Un-select All
            </button>
            <button type="button" class="btn btn-sm btn-success" id="issue-cutting-order">
                Issue Cutting Order
            </button>
            @endcan
    
        </div>
    </nav>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="block_id" id="block_id"
                                        placeholder="Select Block">
                                        <option value="">{{ __('Select Block') }}</option>
                                        @foreach (App\Models\Block::all() as $block)
                                            <option
                                                {{ @request()->block_id && request()->block_id == $block->id ? 'selected' : '' }}
                                                value="{{ $block->id }}">{{ $block->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="gram_panchyat_id" id="gram_panchyat_id"
                                        placeholder="Select Gram Panchyat">
                                        <option value="">{{ __('Select Gram Panchyat') }}</option>
                                        @foreach (App\Models\GramPanchyat::all() as $gp)
                                            <option
                                                {{ @request()->gram_panchyat_id && request()->gram_panchyat_id == $gp->id ? 'selected' : '' }}
                                                value="{{ $gp->id }}">{{ $gp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="village_id" id="village_id"
                                        placeholder="Select Village">
                                        <option value="">{{ __('Select Village') }}</option>
                                        @foreach (App\Models\Village::all() as $village)
                                            <option
                                                {{ @request()->village_id && request()->village_id == $village->id ? 'selected' : '' }}
                                                value="{{ $village->id }}">{{ $village->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="zone_id" id="zone_id"
                                        placeholder="Select Country">
                                        <option value="">{{ __('Select Zone') }}</option>
                                        @foreach (App\Models\Zone::all() as $zone)
                                            <option
                                                {{ @request()->zone_id && request()->zone_id == $zone->id ? 'selected' : '' }}
                                                value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="center_id" id="center_id"
                                        placeholder="Select Center">
                                        <option value="">{{ __('Select Center') }}</option>
                                        @foreach (App\Models\Center::all() as $center)
                                            <option
                                                {{ @request()->center_id && request()->center_id == $center->id ? 'selected' : '' }}
                                                value="{{ $center->id }}">{{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::label('date_of_harvesting', __('Date of Harvesting From'), ['class' => 'form-label']) }}
                                {{ Form::date('date_of_harvesting_from', @request()->date_of_harvesting_from ? \Carbon\Carbon::parse(request()->date_of_harvesting_from)->format('Y-m-d') : '', ['class' => 'form-control', 'id' => 'date_of_harvesting_from']) }}
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::label('date_of_harvesting', __('Date of Harvesting To'), ['class' => 'form-label']) }}
                                {{ Form::date('date_of_harvesting_to', @request()->date_of_harvesting_to ? \Carbon\Carbon::parse(request()->date_of_harvesting_to)->format('Y-m-d') : '', ['class' => 'form-control', 'id' => 'date_of_harvesting_to']) }}
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('seed_category_id', __('Seed Category'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="seed_category_id" id="seed_category_id"
                                        placeholder="Select Seed Category">
                                        <option value="">{{ __('Select Seed Category') }}</option>
                                        @foreach (App\Models\SeedCategory::all() as $seed_category)
                                            <option
                                                {{ @request()->seed_category_id && request()->seed_category_id == $seed_category->id ? 'selected' : '' }}
                                                value="{{ $seed_category->id }}">{{ $seed_category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="type" id="type" placeholder="Select Type">
                                    <option value="">{{ __('Select Type') }}</option>
                                    <option
                                        {{ @request()->type && request()->type == 'Plant' ? 'selected' : '' }}value="Plant">
                                        Plant</option>
                                    <option
                                        {{ @request()->type && request()->type == 'R-1' ? 'selected' : '' }}value="R-1">R-1
                                    </option>
                                    <option
                                        {{ @request()->type && request()->type == 'R-2' ? 'selected' : '' }}value="R-2">R-2
                                    </option>
                                    <option
                                        {{ @request()->type && request()->type == 'R-3' ? 'selected' : '' }}value="R-3">R-3
                                    </option>
                                    <option
                                        {{ @request()->type && request()->type == 'R-4' ? 'selected' : '' }}value="R-4">R-4
                                    </option>
                                    <option
                                        {{ @request()->type && request()->type == 'R-5' ? 'selected' : '' }}value="R-5">R-5
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary ">Search</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table dataTable">
                            <thead>
                                <tr>
                                    <th>
                                        {{ __('Select') }}
                                    </th>
                                    <th>{{ __('Farmer') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Plot Number') }}</th>
                                    <th>{{ __('Kata Number') }}</th>
                                    <th>{{ __('Area in Acar') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Cutting Order') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($farming_details as $farming_detail)
                                    <tr class="font-style">
                                        <td><input type="checkbox" class="farming-detail"
                                                name="farming_detail[{{ $farming_detail->id }}]" id="farming-detail"
                                                value="0"></td>
                                        <td>{{ @$farming_detail->farming->name }}</td>
                                        <td>{{ $farming_detail->name }}</td>
                                        <td>{{ $farming_detail->plot_number }}</td>
                                        <td>{{ $farming_detail->kata_number }}</td>
                                        <td>{{ $farming_detail->area_in_acar }}</td>
                                        <td>{{ $farming_detail->quantity }}</td>
                                        <td>
                                            @if (@$farming_detail->is_cutting_order)
                                                <span
                                                    class="status_badge text-capitalize badge bg-success p-2 px-3 rounded">Yes</span>
                                            @else
                                                <span
                                                    class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">No</span>
                                            @endif
                                        </td>
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
