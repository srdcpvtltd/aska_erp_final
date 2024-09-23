@canany(['manage-product_service', 'manage-product_stock'])
    <li
        class="nav-item {{ Request::segment(2) == 'productservice' || Request::segment(2) == 'productstock' ? 'active' : '' }} ">
        <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#product" role="button" aria-expanded="false"
            aria-controls="company">
            <i class="link-icon" data-feather="users"></i>
            <span class="link-title">{{ __('Product Service') }}</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="{{ Request::segment(2) == 'productservice' || Request::segment(2) == 'productstock' ? '' : 'collapse' }}"
            id="product">
            <ul class="nav sub-menu">
                @can('manage-product_service')
                    <li class="nav-item">
                        <a href="{{ route('admin.productservice.index') }}"
                            class="nav-link {{ Request::segment(2) == 'productservice' ? 'active' : '' }}">{{ __('Product & Services') }}
                        </a>
                    </li>
                @endcan
                @can('manage-product_stock')
                    <li class="nav-item">
                        <a href="{{ route('admin.productstock.index') }}"
                            class="nav-link {{ Request::segment(2) == 'productstock' ? 'active' : '' }}">{{ __('Product Stock') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcanany
