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
                    <div class="col-12 d-flex">
                        <span class="h3">Pages</span>
                        @if($pages->count())
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
                            <table class="table table-bordered">
                                <thead>
                                <tr class="d-flex">
                                    <th scope="col" class="col-4 col-sm-4">Page</th>
                                    <th scope="col" class="col-4 col-sm-3">Completion</th>
                                    <th scope="col" class="col-4 col-sm-5">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pages as $pageName => $page)
                                    <tr class="d-flex" data-page-title="{{$pageName}}">
                                        <td class="col-4 col-sm-4">
                                            {{$pageName}}
                                        </td>
                                        <td class="col-4 col-sm-3">
                                            {{$completions[$pageName]}}
                                        </td>
                                        <td class="col-4 col-sm-5">
                                            <div class="gap-10">
                                                <div class="d-inline-block">
                                                    <a href="{{route('languages-page', ['page' => $pageName])}}"
                                                       class="btn btn-primary">View</a>
                                                </div>
                                                @can('manage-pages')
                                                    <div class="d-inline-block">
                                                        <button data-delete-page="{{$pageName}}" class="btn btn-danger"
                                                                data-toggle="modal" data-target="#delete"
                                                                data-disable-onloading>Delete
                                                        </button>
                                                    </div>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {{ $pages->links() }}
                        @else
                            <p><em>No pages to display. Try adding a new one!</em></p>
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