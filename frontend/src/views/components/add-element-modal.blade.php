@component('laravel-translations-dashboard::components.modal',[
    'id' => 'add-new',
    'title' => 'Add new element',
])
    @slot('content')
        <form action="{{route('languages-element-new')}}">
            <div class="form-group">
                <label for="name">Element name</label>
                <input type="text" class="form-control" id="name" aria-describedby="name_help" name="item"
                       data-disable-onloading/>
                <div class="invalid-feedback"></div>
                <small id="name_help" class="form-text text-muted">Only letters, numbers, dashes and underscores are
                    allowed.
                </small>

                <input type="hidden" name="group" value=""/>
            </div>
        </form>
    @endslot

    @slot('footer')
        <button class="btn btn-success" data-new-confirm data-disable-onloading>Add element</button>
        <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
    @endslot
@endcomponent