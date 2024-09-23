@extends('layouts.master')
@section('title', 'Show Task Detail')

@section('action', 'Detail')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
@endsection

@section('button')
    <div class="breadcrumb-button float-md-end d-md-flex align-items-center">

        @can('edit-task_management')
            <a href="{{ route('admin.tasks.edit', $taskDetail->id) }}">
                <button class="btn btn-sm btn-secondary d-md-flex align-items-center me-2"><i class="link-icon me-1"
                        data-feather="edit"></i>Task Edit</button>
            </a>
        @endcan
        <button class="btn btn-sm btn-secondary d-md-flex align-items-center me-2 checklistAdd">
            <i class="link-icon me-1" data-feather="plus"></i>Create Checklist
        </button>

        @can('edit-task_management')
            <a href="{{ route('admin.task-attachment.create', $taskDetail->id) }}">
                <button class="btn btn-sm btn-secondary d-md-flex align-items-center"><i class="link-icon me-1"
                        data-feather="clipboard"></i>Upload Attachment</button>
            </a>
        @endcan

    </div>
@endsection

@section('main-content')

    <section class="content">
        <?php
        $status = [
            'in_progress' => 'primary',
            'not_started' => 'primary',
            'on_hold' => 'info',
            'cancelled' => 'danger',
            'completed' => 'success',
        ];
        ?>

        @include('admin.section.flash_message')

        @include('admin.task.common.breadcrumb')

        <div class="row position-relative">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="mb-2">{{ ucfirst($taskDetail->name) }}</h3>
                        <ul class="list-unstyled d-flex mb-0">
                            <li class="text-muted me-2">Total Checklist : {{ $taskDetail->taskChecklists->count() }}</li>
                            <li class="text-muted">Completed Checklist: {{ $taskDetail->completedTaskChecklist->count() }}
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="progress mb-3">
                            <div class="progress-bar color2" role="progressbar"
                                style="{{ \App\Helpers\AppHelper::getProgressBarStyle($taskDetail->getTaskProgressInPercentage()) }}"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                <span>{{ $taskDetail->getTaskProgressInPercentage() }} %</span>
                            </div>
                        </div>
                        {!! $taskDetail->description !!}

                        <div class="attachment">
                            <div class="row mb-4">
                                @forelse($images as $key => $imageData)
                                    <div class="col-lg-3 mb-4">
                                        <div class="uploaded-image">
                                            <a href="{{ asset(\App\Models\Attachment::UPLOAD_PATH . $imageData->attachment) }}"
                                                data-lightbox="image-1" data-title="{{ $imageData->attachment }}">
                                                <img class="w-100" style=""
                                                    src="{{ asset(\App\Models\Attachment::UPLOAD_PATH . $imageData->attachment) }}"
                                                    alt="document images">
                                            </a>
                                            <p>{{ $imageData->attachment }}</p>
                                            <a class="documentDelete" id="delete" data-title="Image"
                                                href="{{ route('admin.attachment.delete', $imageData->id) }}">
                                                <i class="link-icon remove-image" data-feather="x"></i>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>

                            <div class="row">
                                @forelse($files as $key => $fileData)
                                    <div class="uploaded-files">
                                        <div class="row align-items-center">
                                            <div class="col-lg-1">
                                                <div class="file-icon">
                                                    <i class="link-icon" data-feather="file-text"></i>
                                                </div>
                                            </div>
                                            <div class="col-lg-10">
                                                <a target="_blank"
                                                    href="{{ asset(\App\Models\Attachment::UPLOAD_PATH . $fileData->attachment) }}">
                                                    {{ asset(\App\Models\Attachment::UPLOAD_PATH . $fileData->attachment) }}
                                                </a>
                                                <p>{{ \App\Helpers\AppHelper::formatDateForView($fileData->created_at) }}
                                                </p>
                                            </div>
                                            <div class="col-lg-1">
                                                <a class="documentDelete" id="delete" data-title="File"
                                                    data-href="{{ route('admin.attachment.delete', $fileData->id) }}">
                                                    <i class="link-icon remove-files" data-feather="x"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 ">
                    <div class="card-body">
                        @include('admin.task.comment_section')
                    </div>
                </div>


                <div class="card checklistTaskAdd mb-4">
                    <div class="card-header">
                        <h4>Task Checklist Lists</h4>
                    </div>
                    <div class="card-body pb-0">
                        @forelse($taskDetail->taskChecklists as $key => $value)
                            <div class="row align-items-center mb-4 border-bottom pb-3">
                                <div class="col-lg-8 d-flex align-items-center"
                                    style="{{ $value->is_completed ? 'text-decoration: Line-Through' : '' }}">
                                    <input type="checkbox" id="checklistToggle" class="me-2 align-middle" name="checklist"
                                        value="0" {{ $value->is_completed ? 'checked' : '' }}
                                        data-href="{{ route('admin.task-checklists.toggle-status', $value->id) }}" />
                                    {{ $value->name }}
                                </div>

                                <div class="col-lg-2 ">
                                    <p>Assigned To </p>
                                    <img class="rounded-circle checklist-image" style="object-fit: cover"
                                        title="{{ $value->taskAssigned->name }}"
                                        src="{{ $value->taskAssigned->avatar
                                            ? asset(\App\Models\User::AVATAR_UPLOAD_PATH . $value->taskAssigned->avatar)
                                            : asset('assets/images/img.png') }}"
                                        alt="profile">
                                </div>

                                <div class="col-lg-2 text-end">
                                    <a href="{{ route('admin.task-checklists.edit', $value->id) }}" title="Edit checklist">
                                        <i class="link-icon" data-feather="edit"></i>
                                    </a>
                                    <a href="{{ route('admin.task-checklists.delete', $value->id) }}"
                                        data-title="Checklist" id="delete" title="Delete checklist">
                                        <i class="link-icon" data-feather="delete"></i>
                                    </a>
                                </div>

                            </div>
                        @empty
                        @endforelse
                        <div class="checklistForm">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-lg-7 col-md-6 mb-4">
                                    <h5>Create task Checklist</h5>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <button class="btn btn-default btn-secondary float-md-end" id="createChecklist">Create
                                        Checklist</button>
                                </div>
                            </div>
                            <div class="formChecklist d-none">
                                <form id="taskAdd" class="forms-sample "
                                    action="{{ route('admin.task-checklists.store') }}" method="POST">
                                    @csrf
                                    <div id="addTaskCheckList">
                                        <input type="hidden" name="task_id" id="taskId"
                                            value="{{ $taskDetail->id }}" />
                                        <div class="row checklist align-items-center justify-content-between">
                                            <div class="col-lg-7 col-md-7 mb-4">
                                                <input type="text" class="form-control" id="name" name="name[]"
                                                    value="" required placeholder="Enter Checklist Title">
                                            </div>
                                            <div class="col-lg-3 col-md-3 mb-4">
                                                <select class="form-select" id="assigned_to" name="assigned_to[]"
                                                    required>
                                                    <option value="" {{ old('assigned_to') ? '' : 'selected' }}
                                                        disabled>Select Member</option>
                                                    @foreach ($taskDetail->assignedMembers as $key => $value)
                                                        <option value="{{ $value->user->id }}"
                                                            {{ old('assigned_to') == $value->user->id ? 'selected' : '' }}>
                                                            {{ $value->user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-2 col-md-2 mb-4 addButtonSection float-md-end">
                                                <button type="button" class="btn btn-xs btn-primary" id="addChecklist"
                                                    title="Add more checklist"> + </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success mb-4" id="checklistSubmit"> Submit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.task.task_summary')
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.task.common.scripts')
    @include('admin.task.common.comment_scripts')
@endsection
