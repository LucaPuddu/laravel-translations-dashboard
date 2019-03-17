<div class="sidebar-logo">
    <div class="logo">Logo</div>
    <i class="icon-left-open sidebar-toggle d-md-none cursor-pointer"></i>
</div>
<div class="sidebar-content">
    <a href="{{route('languages-home')}}" class="sidebar-item transition">
        <i class="sidebar-item-icon icon-home orange"></i> <span class="sidebar-item-text">Dashboard</span>
    </a>
    <a href="{{route('languages-languages')}}" class="sidebar-item transition">
        <i class="sidebar-item-icon icon-globe blue"></i> <span class="sidebar-item-text">Languages</span>
    </a>
    <a href="{{route('languages-pages')}}" class="sidebar-item transition">
        <i class="sidebar-item-icon icon-doc green"></i> <span class="sidebar-item-text">Pages</span>
    </a>
    @can('manage-settings')
        <a href="{{route('languages-settings')}}" class="sidebar-item transition">
            <i class="sidebar-item-icon icon-cog purple"></i> <span class="sidebar-item-text">Settings</span>
        </a>
    @endcan
</div>