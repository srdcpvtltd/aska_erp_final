
@extends('layouts.master')

@section('title','Company')

{{--@section('nav-head','Company')--}}

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Company Profile</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body pb-0">
                <h4 class="mb-4">Company Profile</h4>
                <form class="forms-sample" enctype="multipart/form-data"  method="POST"
                    @if(!$companyDetail)
                          action="{{route('admin.company.store')}}"
                        @else
                          action="{{route('admin.company.update',$companyDetail->id)}}"
                >
                        @method('PUT')
                    @endif

                    @csrf
                    @include('admin.company.form')
                </form>
            </div>
        </div>

    </section>
@endsection

