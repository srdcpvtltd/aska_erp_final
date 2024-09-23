<div class="card overflow-hidden">
<ul class=" nav payroll-sidebar-menu">
    <li class="nav-item {{request()->routeIs('admin.salary-components.*') ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
        <a class="nav-link {{request()->routeIs('admin.salary-components.*') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.salary-components.index')}}">
            Salary Component
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.salary-groups.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
        <a class="nav-link {{ request()->routeIs('admin.salary-groups.*') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.salary-groups.index')}}">
            Salary Group
        </a>
    </li>

    <li class="nav-item {{request()->routeIs('admin.salary-tds.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
        <a class="nav-link {{request()->routeIs('admin.salary-tds.*') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.salary-tds.index')}}">
            Salary TDS
        </a>
    </li>

    <li class="nav-item {{request()->routeIs('admin.overtime.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
        <a class="nav-link {{request()->routeIs('admin.overtime.*') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.overtime.index')}}">
            OverTime
        </a>
    </li>

    <li class="nav-item {{request()->routeIs('admin.under-time.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
        <a class="nav-link {{request()->routeIs('admin.under-time.*') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.under-time.create')}}">
            UnderTime
        </a>
    </li>

    <li class="nav-item {{request()->routeIs('admin.payment-methods.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
        <a class="nav-link {{request()->routeIs('admin.payment-methods.*') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.payment-methods.index')}}">
            Payment Method
        </a>
    </li>

    <li class="nav-item {{request()->routeIs('admin.advance-salaries.setting')  ? 'bg-danger' : '' }} w-100">
        <a class="nav-link {{request()->routeIs('admin.advance-salaries.setting') ? 'text-white' : 'text-black' }}"
           href="{{ route('admin.advance-salaries.setting')}}">
            Advance Salary
        </a>
    </li>

</ul>
</div>
