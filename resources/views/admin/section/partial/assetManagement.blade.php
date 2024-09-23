@canany(['manage-asset_types', 'manage-assets', 'manage-asset_management'])
    <li class="nav-item {{ 
    request()->routeIs('admin.asset-types.*') || 
    request()->routeIs('admin.assets.*') || 
    request()->routeIs('admin.asset_assignment.*')
                        ? 'active' : '' }} ">
        <a class="nav-link" data-bs-toggle="collapse" href="#assets" data-href="#" role="button" aria-expanded="false" aria-controls="assets">
            <i class="link-icon" data-feather="loader"></i>
            <span class="link-title">Asset Management</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="{{ request()->routeIs('admin.asset-types.*') || request()->routeIs('admin.assets.*') || request()->routeIs('admin.asset_assignment.*')
                   ?'' : 'collapse'  }}" id="assets">
            <ul class="nav sub-menu">

                @can('manage-asset_types')
                    <li class="nav-item">
                        <a
                            href="{{route('admin.asset-types.index')}}"
                            data-href="{{route('admin.asset-types.index')}}"
                            class="nav-link {{ request()->routeIs('admin.asset-types.*') ? 'active' : '' }}">Asset Types</a>
                    </li>
                @endcan

                @can('manage-assets')
                    <li class="nav-item">
                        <a href="{{route('admin.assets.index')}}"
                           data-href="{{route('admin.assets.index')}}"
                           class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">Assets</a>
                    </li>
                @endcan

                @can('manage-asset_management')
                    <li class="nav-item">
                        <a href="{{route('admin.asset_assignment.index')}}"
                           data-href="{{route('admin.asset_assignment.index')}}"
                           class="nav-link {{ request()->routeIs('admin.asset_assignment.*') ? 'active' : '' }}">Asset Management</a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcanany
