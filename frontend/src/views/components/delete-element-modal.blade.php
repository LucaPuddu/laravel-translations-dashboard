@component('laravel-translations-dashboard::components.modal',[
    'id' => 'delete',
    'classes' => '',
    'title' => 'Confirm',
])
    @slot('content')
        <input type="hidden" data-action="{{route('languages-element-delete')}}"/>

        <p class="h6">Delete this element?</p>
    @endslot

    @slot('footer')
        <button class="btn btn-danger" data-delete-confirm>Delete</button>
        <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
    @endslot
@endcomponent