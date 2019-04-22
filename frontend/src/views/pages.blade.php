@extends('laravel-translations-dashboard::layout.base')

@section('page', 'pages')

@section('content')
    <div class="translations">
        <div class="row">
            <div class="col col-xl-6">
                <div class="row mar-bottom-10">
                    <div class="col-12">
                        <div class="alert alert-success d-none page-deleted" role="alert">
                            Page successfully deleted.
                        </div>
                        <div class="alert alert-success d-none page-new" role="alert">
                            Page successfully created. <a href="{{url()->current()}}">Reload</a> to see changes.
                        </div>
                    </div>
                    <div class="col-12 mar-bottom-30">
                        <form action="{{route('languages-pages')}}" method="GET">
                            @include('laravel-translations-dashboard::components.search-bar', [
                                'text' => 'Search page...'
                            ])
                        </form>
                    </div>
                    <div class="col-12 d-flex">
                        <span class="h3">Pages</span>
                        @if($languages->count())
                            @can('manage-pages')
                                <button class="btn btn-primary mar-left-auto" data-toggle="modal"
                                        data-target="#add-new">
                                    Add new page
                                </button>
                            @endcan
                        @else
                            <div class="mar-left-auto">
                                <p>No languages yet.</p>
                                <a href="{{route('languages-languages')}}" class="btn btn-primary">
                                    Add new
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @if($pages->count())
                            @include('laravel-translations-dashboard::components.pages')
                        @else
                            @if($searching ?? false)
                                <p><em>No matches.</em></p>
                            @else
                                <p><em>No pages to display. Try adding a new one!</em></p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--DELETE MODAL--}}
    @component('laravel-translations-dashboard::components.modal',[
        'id' => 'delete',
        'classes' => '',
        'title' => 'Delete page',
    ])
        @slot('content')
            <h5>All translations for this page will be deleted as well.</h5>
            <p>Enter <code>{{$confirmation}}</code> to confirm.</p>

            <input type="hidden" data-action="{{route('languages-page-delete')}}"/>
            <input type="hidden" name="confirmation_word" value="{{$confirmation}}"/>

            <input class="form-control" type="text" name="delete_confirmation" autocomplete="off"/>
            <div class="invalid-feedback">
            </div>
        @endslot

        @slot('footer')
            <button class="btn btn-danger" data-delete-confirm>Delete page</button>
            <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
        @endslot
    @endcomponent

    {{--ADD NEW MODAL--}}
    @component('laravel-translations-dashboard::components.modal',[
        'id' => 'add-new',
        'title' => 'Add new page',
    ])
        @slot('content')
            <form action="{{route('languages-page-new')}}">
                <div class="form-group">
                    <label for="name">Page name</label>
                    <input type="text" class="form-control" id="name" aria-describedby="name_help" name="group"
                           data-disable-onloading/>
                    <div class="invalid-feedback"></div>
                    <small id="name_help" class="form-text text-muted">Only letters, numbers, dashes and underscores are
                        allowed.
                    </small>
                </div>
            </form>
        @endslot

        @slot('footer')
            <button class="btn btn-success" data-new-confirm data-disable-onloading>Add page</button>
            <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
        @endslot
    @endcomponent
@endsection