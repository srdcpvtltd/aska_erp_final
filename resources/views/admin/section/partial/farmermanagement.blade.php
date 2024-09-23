@canany(['manage-farmer_registration', 'manage-farmer_guarantor', 'manage-security_deposite', 'manage-allotment',
    'manage-bank_guarantee', 'manage-reimbursement', 'manage-plot', 'manage-issue_cutting_order', 'manage-bank_detail', 'manage-seed_category'])
    <li class="nav-item  {{ request()->routeIs('admin.farmer*') ? 'active' : '' }}   ">
        <a data-href="#" class="nav-link" data-bs-toggle="collapse" href="#farmermanagement" role="button" aria-expanded="false"
            aria-controls="company">
            <i class="link-icon" data-feather="users"></i>
            <span class="link-title">{{ __('Farmer Management') }}</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>

        <div class="{{ request()->routeIs('admin.farmer*') ? '' : 'collapse' }}" id="farmermanagement">
            <ul class="nav sub-menu">
                @can('manage-farmer_registration')
                    <li class="nav-item">
                        <a href="{{ route('admin.farmer.farming_registration.index') }}"
                            class="nav-link {{ request()->routeIs('admin.farmer.farming_registration*') ? 'active' : '' }}">{{ __('Farmer Registration') }}</a>
                    </li>
                @endcan
                @can('manage-farmer_guarantor')
                    <li class="nav-item">
                        <a href="{{ route('admin.farmer.guarantor.index') }}"
                            class="nav-link {{ request()->routeIs('admin.farmer.guarantor*') ? 'active' : '' }}">{{ __('Farmer Guarantor') }}</a>
                    </li>
                @endcan
                @can('manage-plot')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/farmer/farming_detail*') ? ' active' : '' }}"
                            href="{{ route('admin.farmer.farming_detail.index') }}">{{ __('Plot Details') }}</a>
                    </li>
                @endcan
                @can('manage-bank_detail')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/farmer/bank_details*') ? ' active' : '' }}"
                            href="{{ route('admin.farmer.bank_details.index') }}">{{ __('Bank Details') }}</a>
                    </li>
                @endcan
                @can('manage-security_deposite')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/farmer/payment*') ? ' active' : '' }}"
                            href="{{ route('admin.farmer.payment.index') }}">{{ __('Security Deposit') }}</a>
                    </li>
                @endcan
                @can('manage-allotment')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/farmer/loan*') ? ' active' : '' }}"
                            href="{{ route('admin.farmer.loan.index') }}">{{ __('Allotment') }}</a>
                    </li>
                @endcan
                @can('manage-reimbursement')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/farmer/reimbursement*') ? ' active' : '' }}"
                            href="{{ route('admin.farmer.reimbursement.index') }}">{{ __('Reimbursement') }}</a>
                    </li>
                @endcan
                @can('manage-issue_cutting_order')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/farmer/cutting_order*') ? ' active' : '' }}"
                            href="{{ route('admin.farmer.cutting_order.index') }}">{{ __('Issue Cutting Order') }}</a>
                    </li>
                @endcan
                @can('manage-seed_category')
                <li class="nav-item">
                    <a href="{{ route('admin.farmer.seed_category.index') }}"
                        class="nav-link {{ request()->routeIs('admin.farmer.seed_category.*') ? 'active' : '' }}">
                        {{ __('Seed Category') }}
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </li>
@endcanany
