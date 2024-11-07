

    <!-- sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="{{route('admin.dashboard')}}" class="sidebar-brand">
               <img src="{{asset(\App\Models\Company::UPLOAD_PATH.\App\Helpers\AppHelper::getCompanyLogo())}}" />
            </a>
            <div class="sidebar-toggler not-active">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="sidebar-body">
            <ul class="nav sidebar-menu">
                @include('admin.section.partial.dashboard')
                @include('admin.section.partial.hrm')
                @include('admin.section.partial.project&task_management')
                @include('admin.section.partial.assetManagement')
                @include('admin.section.partial.farmermanagement')
                @include('admin.section.partial.inventory')
                @include('admin.section.partial.product')
                @include('admin.section.partial.setting')
                @include('admin.section.partial.accountingsystem')
            </ul>
        </div>
    </nav>
    <!-- partial -->


