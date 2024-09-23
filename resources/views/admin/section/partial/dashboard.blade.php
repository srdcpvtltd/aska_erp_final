<li
    class="nav-item {{ Request::segment(2) == 'dashboard' || Request::segment(2) == 'farmer_dashboard' || Request::segment(2) == 'account_dashboard' || Request::segment(2) == 'hr_dashboard' ? 'active' : '' }} ">
    <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#dashboard" role="button" aria-expanded="false"
        aria-controls="company">
        <i class="link-icon" data-feather="users"></i>
        <span class="link-title">{{ __('Dashboard') }}</span>
        <i class="link-arrow" data-feather="chevron-down"></i>
    </a>

    <div class="{{ Request::segment(2) == 'dashboard' || Request::segment(2) == 'farmer_dashboard' || Request::segment(2) == 'account_dashboard' || Request::segment(2) == 'hr_dashboard' ? '' : 'collapse' }}"
        id="dashboard">
        <ul class="nav sub-menu">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ Request::segment(2) == 'dashboard' ? 'active' : '' }}">{{ __('Dashboard') }}
                </a>
            </li>
            @can('show-hr dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.hr_dashboard') }}"
                        class="nav-link {{ Request::segment(2) == 'hr_dashboard' ? 'active' : '' }}">{{ __('HR Dashboard') }}
                    </a>
                </li>
            @endcan
            @can('show-farmer dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard_farmer') }}"
                        class="nav-link {{ Request::segment(2) == 'farmer_dashboard' ? 'active' : '' }}">{{ __('Farmer Dashboard') }}
                    </a>
                </li>
            @endcan
            @can('show-account dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.account_dashboard') }}"
                        class="nav-link {{ Request::segment(2) == 'account_dashboard' ? 'active' : '' }}">{{ __('Account Dashboard') }}
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</li>
