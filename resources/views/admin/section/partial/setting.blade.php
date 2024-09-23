@canany(['manage-roles', 'manage-general_settings'])
    <li
        class="nav-item  {{ request()->routeIs('admin.roles.*') ||
        request()->routeIs('admin.notifications.*') ||
        request()->routeIs('admin.general-settings.*') ||
        request()->routeIs('admin.payment-currency.*') ||
        request()->routeIs('admin.app-settings.*') ||
        request()->routeIs('admin.feature.*') ||
        request()->routeIs('admin.modules.*') ||
        request()->routeIs('admin.static-page-contents.*') ||
        request()->routeIs('admin.banks.*') ||
        request()->routeIs('admin.bank_branches.*') ||
        request()->routeIs('admin.location.country.*') ||
        request()->routeIs('admin.location.state.*') ||
        request()->routeIs('admin.location.district.*') ||
        request()->routeIs('admin.location.block.*') ||
        request()->routeIs('admin.location.gram_panchyat.*') ||
        request()->routeIs('admin.location.village.*') ||
        request()->routeIs('admin.location.zone.*') ||
        request()->routeIs('admin.location.center.*') ||
        request()->routeIs('admin.irrigation.*') 
            ? 'active'
            : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#setting" data-href="#" role="button" aria-expanded="false"
            aria-controls="settings">
            <i class="link-icon" data-feather="settings"></i>
            <span class="link-title"> Setting </span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="{{ request()->routeIs('admin.roles.*') ||
        request()->routeIs('admin.notifications.*') ||
        request()->routeIs('admin.general-settings.*') ||
        request()->routeIs('admin.payment-currency.*') ||
        request()->routeIs('admin.app-settings.*') ||
        request()->routeIs('admin.feature.*') ||
        request()->routeIs('admin.modules.*') ||
        request()->routeIs('admin.static-page-contents.*') ||
        request()->routeIs('admin.banks.*') ||
        request()->routeIs('admin.bank_branches.*') ||
        request()->routeIs('admin.location.country.*') ||
        request()->routeIs('admin.location.state.*') ||
        request()->routeIs('admin.location.district.*') ||
        request()->routeIs('admin.location.block.*') ||
        request()->routeIs('admin.location.gram_panchyat.*') ||
        request()->routeIs('admin.location.village.*') ||
        request()->routeIs('admin.location.zone.*') ||
        request()->routeIs('admin.location.center.*') ||
        request()->routeIs('admin.irrigation.*') 
            ? ''
            : 'collapse' }} "
            id="setting">

            <ul class="nav sub-menu">
                @can('manage-roles')
                    <li class="nav-item">
                        <a href="{{ route('admin.roles.index') }}" data-href="{{ route('admin.roles.index') }}"
                            class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Roles & Permissions</a>
                    </li>
                @endcan
                @can('manage-modules')
                    <li class="nav-item">
                        <a href="{{ route('admin.modules.index') }}" data-href="{{ route('admin.modules.index') }}"
                            class="nav-link {{ request()->routeIs('admin.modules.*') ? 'active' : '' }}">
                            <span>Modules</span>
                        </a>
                    </li>
                @endcan
                @can('manage-general_settings')
                    <li class="nav-item">
                        <a href="{{ route('admin.general-settings.index') }}"
                            data-href="{{ route('admin.general-settings.index') }}"
                            class="nav-link {{ request()->routeIs('admin.general-settings.*') ? 'active' : '' }}">General
                            Settings</a>
                    </li>
                @endcan

                <li class="nav-item">
                    <a href="{{ route('admin.app-settings.index') }}" data-href="{{ route('admin.app-settings.index') }}"
                        class="nav-link {{ request()->routeIs('admin.app-settings.*') ? 'active' : '' }}">App Settings</a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.notifications.index') }}"
                        data-href="{{ route('admin.notifications.index') }}"
                        class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">Notifications</a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.payment-currency.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payment-currency.index') }}"
                        data-href="{{ route('admin.payment-currency.index') }}"
                        class="nav-link {{ request()->routeIs('admin.payment-currency.*') ? 'active' : '' }}"> Payment
                        Currency</a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.feature.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.feature.index') }}" data-href="{{ route('admin.feature.index') }}"
                        class="nav-link {{ request()->routeIs('admin.feature.index') ? 'active' : '' }}"> Feature
                        Control</a>
                </li>
                @canany('manage-bank', 'manage-bank_branch')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::segment(2) == 'banks' || Request::segment(2) == 'bank_branches' ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#bank" data-href="#" role="button" aria-expanded="false">
                            <span> {{ __('Bank') }} </span>
                            <i class="link-arrow" data-feather="chevron-down"></i>
                        </a>
                        <div class="{{ Request::segment(2) == 'banks' || Request::segment(2) == 'bank_branches' ? '' : 'collapse' }}"
                            id="bank">
                            <ul class="nav sub-menu">
                                @can('manage-bank')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.banks.index' || Request::route()->getName() == 'admin.banks.create' || Request::route()->getName() == 'admin.banks.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.banks.index') }}">{{ __('Banks') }}</a>
                                    </li>
                                @endcan
                                @can('manage-bank_branch')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.bank_branches.index' || Request::route()->getName() == 'admin.bank_branches.create' || Request::route()->getName() == 'admin.bank_branches.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.bank_branches.index') }}">{{ __('Branches') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany('manage-country', 'manage-state', 'manage-district', 'manage-block', 'manage-gram_panchyat',
                    'manage-village', 'manage-zone', 'manage-center')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.location.country.*') || request()->routeIs('admin.location.state.*') || request()->routeIs('admin.location.district.*') || request()->routeIs('admin.location.block.*') || request()->routeIs('admin.location.gram_panchyat.*') || request()->routeIs('admin.location.village.*') || request()->routeIs('admin.location.zone.*') || request()->routeIs('admin.location.center.*') ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#location" data-href="#" role="button" aria-expanded="false">
                            <span> {{ __('Location') }} </span>
                            <i class="link-arrow" data-feather="chevron-down"></i>
                        </a>
                        <div class="{{ request()->routeIs('admin.location.country.*') || request()->routeIs('admin.location.state.*') || request()->routeIs('admin.location.district.*') || request()->routeIs('admin.location.block.*') || request()->routeIs('admin.location.gram_panchyat.*') || request()->routeIs('admin.location.village.*') || request()->routeIs('admin.location.zone.*') || request()->routeIs('admin.location.center.*') ? '' : 'collapse' }}"
                            id="location">
                            <ul class="nav sub-menu">
                                @can('manage-country')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.country.index' || Request::route()->getName() == 'admin.location.country.create' || Request::route()->getName() == 'admin.location.country.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.country.index') }}">{{ __('Country') }}</a>
                                    </li>
                                @endcan
                                @can('manage-state')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.state.index' || Request::route()->getName() == 'admin.location.state.create' || Request::route()->getName() == 'admin.location.state.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.state.index') }}">{{ __('State') }}</a>
                                    </li>
                                @endcan
                                @can('manage-district')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.district.index' || Request::route()->getName() == 'admin.location.district.create' || Request::route()->getName() == 'admin.location.district.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.district.index') }}">{{ __('District') }}</a>
                                    </li>
                                @endcan
                                @can('manage-block')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.block.index' || Request::route()->getName() == 'admin.location.block.create' || Request::route()->getName() == 'admin.location.block.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.block.index') }}">{{ __('Block') }}</a>
                                    </li>
                                @endcan
                                @can('manage-gram_panchyat')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.gram_panchyat.index' || Request::route()->getName() == 'admin.location.gram_panchyat.create' || Request::route()->getName() == 'admin.location.gram_panchyat.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.gram_panchyat.index') }}">{{ __('Gram Panchayat') }}</a>
                                    </li>
                                @endcan
                                @can('manage-village')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.village.index' || Request::route()->getName() == 'admin.location.village.create' || Request::route()->getName() == 'admin.location.village.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.village.index') }}">{{ __('Village') }}</a>
                                    </li>
                                @endcan
                                @can('manage-zone')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.zone.index' || Request::route()->getName() == 'admin.location.zone.create' || Request::route()->getName() == 'admin.location.zone.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.zone.index') }}">{{ __('Zone') }}</a>
                                    </li>
                                @endcan
                                @can('manage-center')
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::route()->getName() == 'admin.location.center.index' || Request::route()->getName() == 'admin.location.center.create' || Request::route()->getName() == 'admin.location.center.edit' ? ' active' : '' }}"
                                            href="{{ route('admin.location.center.index') }}">{{ __('Center') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany

                @can('manage-content_management')
                    <li class="nav-item">
                        <a href="{{ route('admin.static-page-contents.index') }}"
                            data-href="{{ route('admin.static-page-contents.index') }}"
                            class="nav-link {{ request()->routeIs('admin.static-page-contents.*') ? 'active' : '' }}">
                            Content Management
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a href="{{ route('admin.irrigation.index') }}"
                        class="nav-link {{ request()->routeIs('admin.irrigation.*') ? 'active' : '' }}">
                        Irrigation
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endcanany
