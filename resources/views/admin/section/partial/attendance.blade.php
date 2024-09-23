@canany(['manage-attendance', 'manage-regularization'])
    <li
        class="nav-item {{ request()->routeIs('admin.attendances.*') || request()->routeIs('admin.regularization.*') ? 'active' : '' }}">
        <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#attendance_management" role="button"
            aria-expanded="false" aria-controls="company">
            <i class="link-icon" data-feather="calendar"></i>
            <span class="link-title">Attendance Section</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="{{ request()->routeIs('admin.attendances.*') || request()->routeIs('admin.regularization.*') ? '' : 'collapse' }}"
            id="attendance_management">
            <ul class="nav sub-menu">
                @can('manage-attendance')
                    <li class="nav-item">
                        <a href="{{ route('admin.attendances.index') }}" data-href="{{ route('admin.attendances.index') }}"
                            class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">Attendance</a>
                    </li>
                @endcan

                @can('manage-regularization')
                    <li class="nav-item">
                        <a href="{{ route('admin.regularization.index') }}"
                            data-href="{{ route('admin.regularization.index') }}"
                            class="nav-link {{ request()->routeIs('admin.regularization.*') ? 'active' : '' }}">Regularization</a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcanany
