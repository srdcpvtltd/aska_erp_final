@canany(['manage-routers', 'manage-nfc', 'manage-qr'])
<li
    class="nav-item  {{ request()->routeIs('admin.routers.*') ||
    request()->routeIs('admin.qr.*') ||
    request()->routeIs('admin.nfc.*')
        ? 'active'
        : '' }}">
    <a class="nav-link" data-bs-toggle="collapse" href="#attendance_method" data-href="#" role="button"
        aria-expanded="false" aria-controls="settings">
        <i class="link-icon" data-feather="tool"></i>
        <span class="link-title"> Attendance Methods </span>
        <i class="link-arrow" data-feather="chevron-down"></i>
    </a>
    <div class="{{ request()->routeIs('admin.routers.*') ||
    request()->routeIs('admin.qr.*') ||
    request()->routeIs('admin.nfc.*')
        ? ''
        : 'collapse' }} "
        id="attendance_method">

        <ul class="nav sub-menu">

            @can('manage-routers')
                <li class="nav-item">
                    <a href="{{ route('admin.routers.index') }}" data-href="{{ route('admin.routers.index') }}"
                        class="nav-link {{ request()->routeIs('admin.routers.*') ? 'active' : '' }}">Routers
                    </a>
                </li>
            @endcan

            @can('manage-nfc')
                <li class="nav-item">
                    <a href="{{ route('admin.nfc.index') }}" data-href="{{ route('admin.nfc.index') }}"
                        class="nav-link {{ request()->routeIs('admin.nfc.*') ? 'active' : '' }}">NFC</a>
                </li>
            @endcan

            @can('manage-qr')
                <li class="nav-item">
                    <a href="{{ route('admin.qr.index') }}" data-href="{{ route('admin.qr.index') }}"
                        class="nav-link {{ request()->routeIs('admin.qr.*') ? 'active' : '' }}">QR</a>
                </li>
            @endcan
        </ul>
    </div>
</li>
@endcanany