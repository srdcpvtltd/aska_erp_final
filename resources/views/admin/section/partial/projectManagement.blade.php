@can('manage-project_management')
    <li class="nav-item {{ request()->routeIs('admin.projects.*')  ? 'active' : '' }}">
        <a
            href="{{ route('admin.projects.index') }}"
            data-href="{{ route('admin.projects.index') }}"
            class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Project Management</span>
        </a>
</li>
@endcan
