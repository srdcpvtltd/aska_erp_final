@can('manage-notice')
    <li class="nav-item {{ request()->routeIs('admin.notices.*')  ? 'active' : '' }}">
        <a
            href="{{ route('admin.notices.index') }}"
            data-href="{{ route('admin.notices.index') }}"
            class="nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
            <i class="link-icon" data-feather="calendar"></i>
            <span class="link-title">Notice</span>
        </a>
    </li>
@endcan
