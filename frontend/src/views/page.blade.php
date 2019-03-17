@extends('laravel-translations-dashboard::layout.base')

@section('page', 'page')

@section('page-styles')
    @if($rich_editor)
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/pell/dist/pell.min.css">
    @endif
@endsection

@section('page-body-scripts')
    @if($rich_editor)
        <script src="https://unpkg.com/pell"></script>
    @endif
@endsection

@section('content')
    <input type="hidden" name="manage_pages" @can('manage-pages') value="1" @else value="0" @endcan>
    <div class="page" data-page="{{$page}}" data-save-action="{{route('languages-element-edit')}}">
        <div class="row">
            <div class="col-10 mar-bottom-10">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success d-none element-deleted" role="alert">
                            Element successfully deleted.
                        </div>
                        <div class="alert alert-error d-none element-deleted" role="alert">
                            Error with saving element.
                        </div>
                        <div class="alert alert-success d-none element-new" role="alert">
                            Element successfully created.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-10 mar-bottom-10">
                <div class="d-inline-block">
                    <h1 class="h3"><span class="pre">{{$page}}</span> page translations</h1>
                </div>
                <div class="float-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#add-new">Add new element</button>
                </div>
            </div>
            <div class="col-12 form mar-bottom-20">
                <form action="{{url()->current()}}" method="GET"
                      id="language-form">
                    <div class="row margin-bottom-md align-items-center">
                        <div class="col-2">
                            <strong>Element</strong>
                        </div>
                        <div class="col-4">
                            <div class="row align-items-center">
                                <div class="col-auto"><strong>From</strong></div>
                                <div class="col-auto">
                                    <select class="form-control" name="origin">
                                        @foreach($languages as $language)
                                            <option value="{{$language->locale}}"
                                                    @if($origin_language->locale === $language->locale) selected @endif>{{$language->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row align-items-center justify-content-end">
                                <div class="col-auto"><strong>To</strong></div>
                                <div class="col-auto">
                                    <select class="form-control" name="destination">
                                        @foreach($languages as $language)
                                            <option value="{{$language->locale}}"
                                                    @if($destination_language->locale === $language->locale) selected @endif>{{$language->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <strong>Actions</strong>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 form">
                <form action="{{route('languages-page-edit')}}" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-12" id="elements-container">
                            @foreach($items as $item)
                                <div class="row mar-bottom-20 align-items-stretch" data-item="{{$item}}">
                                    <div class="col-2">
                                        <span class="pre h6">{{$item}}</span>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control origin-text">
                                            {!! isset($origin_translations[$item]) ? $origin_translations[$item]->text : '' !!}
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        @if($rich_editor)
                                            <div data-pell>
                                                <span data-content>{{isset($destination_translations[$item]) ? $destination_translations[$item]->text : ''}}</span>
                                            </div>
                                        @else
                                            <textarea data-content
                                                      class="form-control">{{isset($destination_translations[$item]) ? $destination_translations[$item]->text : ''}}</textarea>
                                        @endif
                                        <div class="loader"></div>
                                    </div>
                                    <div class="col-2 gap-3">
                                        <div class="d-inline-block">
                                            <button class="btn btn-primary" type="button" data-save-item="{{$item}}">
                                                Save
                                            </button>
                                        </div>
                                        @can('manage-pages')
                                            <div class="d-inline-block">
                                                <button type="button" data-delete-item="{{$item}}"
                                                        class="btn btn-danger"
                                                        data-toggle="modal" data-target="#delete">Delete
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--DELETE MODAL--}}
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

    {{--ADD NEW MODAL--}}
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

                    <input type="hidden" name="group" value="{{$page}}"/>
                </div>
            </form>
        @endslot

        @slot('footer')
            <button class="btn btn-success" data-new-confirm data-disable-onloading>Add element</button>
            <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
        @endslot
    @endcomponent
@endsection