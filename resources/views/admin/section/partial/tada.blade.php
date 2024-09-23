@can('manage-tada')
    <li class="nav-item">
        <a
            href="{{ route('admin.tadas.index') }}"
            data-href="{{ route('admin.tadas.index') }}"
            class="nav-link {{ request()->routeIs('admin.tadas.*')  ? 'active' : '' }}">
            <i class="link-icon" data-feather="file-text"></i>
            <span class="link-title">Tada</span>
        </a>
    </li>
@endcan
