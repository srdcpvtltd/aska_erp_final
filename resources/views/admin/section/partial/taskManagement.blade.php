@can('manage-task_management')
    <li class="nav-item {{ request()->routeIs('admin.tasks.*')  ? 'active' : '' }}">
        <a
            href="{{ route('admin.tasks.index') }}"
            data-href="{{ route('admin.tasks.index') }}"
            class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
            <i class="link-icon" data-feather="table"></i>
            <span class="link-title">Task Management</span>
        </a>
    </li>
@endcan

