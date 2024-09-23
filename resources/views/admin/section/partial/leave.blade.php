@canany(['manage-leave_types', 'manage-leave_request', 'manage-time_leave_request'])
    <li
        class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.leaves.*') ||
        request()->routeIs('admin.time-leave-request.*') ||
        request()->routeIs('admin.leave-request.*')
            ? 'active'
            : '' }} " href="{{ route('admin.leaves.index') }}" aria-controls="leaves">
            <i class="link-icon" data-feather="bookmark"></i>
            <span class="link-title">Leave</span>
        </a>
    </li>
@endcanany
