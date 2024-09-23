@can('manage-client')
    <li class="nav-item {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
        <a href="{{ route('admin.clients.index') }}" data-href="{{ route('admin.clients.index') }}"
            class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
            <i class="link-icon" data-feather="heart"></i>
            <span class="link-title">Clients</span>
        </a>
    </li>
@endcan
