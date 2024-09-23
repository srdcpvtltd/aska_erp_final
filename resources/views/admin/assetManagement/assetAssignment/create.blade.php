@extends('layouts.master')

@section('title','Create Asset Assignment')

@section('action','Create')

@section('main-content')

<section class="content">

    @include('admin.section.flash_message')

    @include('admin.assetManagement.assetDetail.common.breadcrumb')

    <div class="card">
        <div class="card-body">
            <form id="" class="forms-sample" action="{{route('admin.asset_assignment.store')}}" enctype="multipart/form-data" method="POST">
                @csrf
                @include('admin.assetManagement.assetAssignment.form')
            </form>
        </div>
    </div>

</section>
@endsection

@section('scripts')
@include('admin.assetManagement.assetAssignment.scripts')
@endsection