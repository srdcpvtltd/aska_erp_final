
@can('manage-team_meeting')
    <li class="nav-item">
        <a
            href="{{ route('admin.team-meetings.index') }}"
            data-href="{{ route('admin.team-meetings.index') }}"
            class="nav-link {{ request()->routeIs('admin.team-meetings.*')  ? 'active' : '' }}">
            <i class="link-icon" data-feather="globe"></i>
            <span class="link-title">Team Meeting</span>
        </a>
    </li>
@endcan
