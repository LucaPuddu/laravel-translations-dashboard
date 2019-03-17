<div id="{{$id}}" class="modal {{$classes ?? ''}}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$title ?? ''}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{$content}}
            </div>
            @if (isset($footer))
                <div class="modal-footer">
                    {{$footer}}
                </div>
            @endif
        </div>
    </div>
</div>