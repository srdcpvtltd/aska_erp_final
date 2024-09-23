
@extends('layouts.master')

@section('title','Edit Department')

@section('button')
    <a href="{{route('admin.departments.index')}}" >
        <button class="btn btn-sm btn-primary" ><i class="link-icon" data-feather="arrow-left"></i> Back</button>
    </a>
@endsection

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.departments.index')}}">Department section</a></li>
                <li class="breadcrumb-item active" aria-current="page">Department Edit</li>
            </ol>
        
            @yield('button')
        </nav>
        <div class="card">
            <div class="card-body pb-0">
                <form class="forms-sample" action="{{route('admin.departments.update',$departmentsDetail->id)}}" enctype="multipart/form-data" method="post">
                    @method('PUT')
                    @csrf
                    @include('admin.department.common.form')
                </form>
            </div>
        </div>

    </section>
@endsection

