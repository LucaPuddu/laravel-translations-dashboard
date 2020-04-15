<i class="icon-menu sidebar-toggle cursor-pointer"></i>
<div class="mar-left-auto">
    <form action="{{config('laravel-translations-dashboard.logout_route')}}" method="POST">
        @csrf
        <button class="btn btn-link btn-logout text-muted">Logout</button>
    </form>
</div>
