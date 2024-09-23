@can('manage-holidays')
    <li class="nav-item {{ request()->routeIs('admin.holidays.*')  ? 'active' : '' }}">
        <a
            href="{{ route('admin.holidays.index') }}"
            data-href="{{ route('admin.holidays.index') }}"
            class="nav-link {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
            <i class="link-icon" data-feather="credit-card"></i>
            <span class="link-title">Holidays</span>
        </a>
    </li>
@endcan
