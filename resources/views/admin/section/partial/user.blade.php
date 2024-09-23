@can('manage-employees')
    <li
        class="nav-item  {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.logout-requests.*') ? 'active' : '' }}   ">
        <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#employee_management" role="button"
            aria-expanded="false" aria-controls="company">
            <i class="link-icon" data-feather="users"></i>
            <span class="link-title">Employee Management</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="{{ request()->routeIs('admin.users.*') || request()->routeIs('admin.logout-requests.*') ? '' : 'collapse' }}"
            id="employee_management">
            <ul class="nav sub-menu">
                @can('manage-employees')
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" data-href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Employees</a>
                    </li>
                @endcan

                <li class="nav-item">
                    <a href="{{ route('admin.logout-requests.index') }}"
                        data-href="{{ route('admin.logout-requests.index') }}"
                        class="nav-link {{ request()->routeIs('admin.logout-requests.*') ? 'active' : '' }}">Logout
                        Requests</a>
                </li>
            </ul>
        </div>
    </li>
@endcanany
