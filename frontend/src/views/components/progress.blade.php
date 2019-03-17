<div class="translations-dashboard-progress">
    <p class="quantity">{{$quantity}}</p>
    <small class="text-muted">{{$description}}</small>
    <span class="float-right">{{$percent}}</span>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="{{$percent}}" aria-valuemin="0"
             aria-valuemax="100" style="width:{{$percent}}%">
            <span class="sr-only">{{$percent}} Complete</span>
        </div>
    </div>
</div>