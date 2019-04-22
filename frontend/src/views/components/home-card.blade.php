@component('laravel-translations-dashboard::components.card', ['url' => $url ?? null])
    <div class="row translations-dashboard-home-card no-gutters align-items-center">
        <div class="col-auto">
            <div class="summary-icon bg-{{$color ?? 'blue'}}">
                <i class="icon-{{$icon ?? 'globe'}}"></i>
            </div>
        </div>
        <div class="col">
            <p class="title">{{$title}}</p>
            <p class="value">{{$quantity}}</p>
            @if(isset($footer))
                <div class="summary-footer">
                    {{$footer}}
                </div>
            @endif
        </div>
    </div>
@endcomponent