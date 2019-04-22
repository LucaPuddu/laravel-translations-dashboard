<div class="translations-dashboard-card @if(isset($url)) linked @endif">
    {{$slot}}

    @if(isset($url))
        <a href="{{$url}}" class="link"></a>
    @endif
</div>