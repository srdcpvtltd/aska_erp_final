@canany(['manage-warehouse', 'manage-purchase','manage-transfer', 'manage-print_barcode', 'manage-print_settings'])
<li
    class="nav-item {{ Request::segment(2) == 'warehouse' || Request::segment(2) == 'purchase' || Request::segment(2) == 'warehouse-transfer' || Request::route()->getName() == 'admin.pos.barcode' || Request::route()->getName() == 'admin.pos.print.setting' || Request::route()->getName() == 'admin.pos.show' ? 'active' : '' }} ">
    <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#inventory" role="button" aria-expanded="false"
        aria-controls="company">
        <i class="link-icon" data-feather="users"></i>
        <span class="link-title">{{ __('Inventory') }}</span>
        <i class="link-arrow" data-feather="chevron-down"></i>
    </a>

    <div class="{{ Request::segment(2) == 'warehouse' || Request::segment(2) == 'purchase' || Request::segment(2) == 'warehouse-transfer' || Request::route()->getName() == 'admin.pos.barcode' || Request::route()->getName() == 'admin.pos.print.setting' || Request::route()->getName() == 'admin.pos.show' ? '' : 'collapse' }}"
        id="inventory">
        <ul
            class="nav sub-menu">
            @can('manage-warehouse')
            <li
                class="nav-item">
                <a class="nav-link {{ Request::route()->getName() == 'admin.warehouse.index' || Request::route()->getName() == 'admin.warehouse.show' ? ' active' : '' }}" href="{{ route('admin.warehouse.index') }}">{{ __('Warehouse') }}</a>
            </li>
            @endcan
            @can('manage-purchase')
            <li
                class="nav-item">
                <a class="nav-link {{ Request::route()->getName() == 'admin.purchase.index' || Request::route()->getName() == 'admin.purchase.create' || Request::route()->getName() == 'admin.purchase.edit' || Request::route()->getName() == 'admin.purchase.show' ? ' active' : '' }}" href="{{ route('admin.purchase.index') }}">{{ __('Purchase') }}</a>
            </li>
            @endcan

            @can('manage-transfer')
            <li
                class="nav-item">
                <a class="nav-link {{ Request::route()->getName() == 'admin.warehouse-transfer.index' || Request::route()->getName() == 'admin.warehouse-transfer.show' ? ' active' : '' }}" href="{{ route('admin.warehouse-transfer.index') }}">{{ __('Transfer') }}</a>
            </li>
            @endcan
            @can('manage-print_barcode')
            <li
                class="nav-item">
                <a class="nav-link {{ Request::route()->getName() == 'admin.pos.barcode' || Request::route()->getName() == 'admin.pos.print' ? ' active' : '' }}" href="{{ route('admin.pos.barcode') }}">{{ __('Print Barcode') }}</a>
            </li>
            @endcan
            @can('manage-print_settings')
            <li class="nav-item">
                <a class="nav-link {{ Request::route()->getName() == 'admin.pos.print.setting' ? ' active' : '' }}" href="{{ route('admin.pos.print.setting') }}">{{ __('Print Settings') }}</a>
            </li>
            @endcan
        </ul>
    </div>
</li>
@endcanany
