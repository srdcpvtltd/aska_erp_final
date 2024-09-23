<li
    class="nav-item {{ request()->routeIs('admin.projects.*') ||
    request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
    <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#management" role="button" aria-expanded="false"
        aria-controls="company">
        <i class="link-icon" data-feather="users"></i>
        <span class="link-title">{{ __('Project & Task Management') }}</span>
        <i class="link-arrow" data-feather="chevron-down"></i>
    </a>

    <div class="{{ request()->routeIs('admin.projects.*') ||
    request()->routeIs('admin.tasks.*') ? '' : 'collapse' }}"
        id="management">
        <ul class="nav sub-menu">
            @include('admin.section.partial.projectManagement')
            @include('admin.section.partial.taskManagement')
        </ul>
    </div>
</li>
