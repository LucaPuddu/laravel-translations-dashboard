@extends('laravel-translations-dashboard::layout.base')

@section('page', 'languages')

@section('content')
    <div class="languages">
        <div class="row">
            <div class="col col-xl-7">
                <div class="row mar-bottom-10">
                    <div class="col-12">
                        <div class="alert alert-success d-none language-deleted" role="alert">
                            Language successfully deleted.
                        </div>
                        <div class="alert alert-success d-none language-new" role="alert">
                            Language successfully created. <a href="{{url()->current()}}">Reload</a> to see changes.
                        </div>
                    </div>

                    <div class="col-12 d-flex">
                        <span class="h3">Languages</span>
                        @can('manage-languages')
                            <button class="btn btn-primary mar-left-auto" data-toggle="modal" data-target="#add-new">Add
                                new
                                language
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @if($languages->count())
                            <table class="table table-bordered" data-action="{{route('languages-language-edit')}}">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Locale</th>
                                    <th scope="col">
                                        Name
                                        <small class="text-muted">(for internal use)</small>
                                    </th>
                                    <th scope="col">Visible</th>
                                    @can('manage-languages')
                                        <th scope="col">Actions</th>@endcan
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($languages as $language)
                                    <tr data-lang-id="{{$language->id}}">
                                        <th scope="row">{{$language->id}}</th>
                                        <td>
                                            <input class="form-control" type="text" name="locale"
                                                   value="{{$language->locale}}"
                                                   @cannot('manage-languages') readonly
                                                   @endcannot data-disable-onloading/>
                                            <div class="invalid-feedback">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" name="name"
                                                   value="{{$language->name}}"
                                                   @cannot('manage-languages') readonly
                                                   @endcannot data-disable-onloading/>
                                            <div class="invalid-feedback">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control" type="checkbox" name="visible" value="1"
                                                   @if($language->visible) checked
                                                   @endif @cannot('manage-languages') disabled
                                                   @endcannot data-disable-onloading/>
                                            <div class="invalid-feedback">
                                            </div>
                                        </td>
                                        @can('manage-languages')
                                            <td>
                                                <div class="gap-10">
                                                    <div class="d-inline-block">
                                                        <button class="btn btn-primary" data-save-id="{{$language->id}}"
                                                                data-disable-onloading>Save
                                                        </button>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <button class="btn btn-danger"
                                                                data-delete-id="{{$language->id}}"
                                                                data-toggle="modal" data-target="#delete"
                                                                data-disable-onloading>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {{ $languages->links() }}
                        @else
                            <p><em>No languages to display. Try adding a new one!</em></p>
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
        'title' => 'Delete language',
    ])
        @slot('content')
            <h5>All translations for this language will be deleted as well.</h5>
            <p>Enter <code>{{$confirmation}}</code> to confirm.</p>

            <input type="hidden" data-action="{{route('languages-language-delete')}}"/>
            <input type="hidden" name="confirmation_word" value="{{$confirmation}}"/>

            <input class="form-control" type="text" name="delete_confirmation" autocomplete="off"/>
            <div class="invalid-feedback">
            </div>
        @endslot

        @slot('footer')
            <button class="btn btn-danger" data-delete-confirm>Delete language</button>
            <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
        @endslot
    @endcomponent

    {{--ADD NEW MODAL--}}
    @component('laravel-translations-dashboard::components.modal',[
        'id' => 'add-new',
        'title' => 'Add new language',
    ])
        @slot('content')
            <h6 class="text-center">Select from list</h6>
            <select class="form-control select2" name="locale">
                @foreach($locales as $locale => $name)
                    <option value="{{$locale}}">{{$name}}</option>
                @endforeach
            </select>
            @include('laravel-translations-dashboard::components.or-separator')
            <h6 class="text-center">Enter manually</h6>
            <form action="{{route('languages-language-new')}}">
                <div class="row font-weight-bold mar-bottom-10">
                    <div class="col">
                        Locale
                    </div>
                    <div class="col">
                        Name
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="locale" data-disable-onloading/>
                        <div class="invalid-feedback">
                        </div>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="name" data-disable-onloading />
                        <div class="invalid-feedback">
                        </div>
                    </div>
                </div>
            </form>
        @endslot

        @slot('footer')
            <button class="btn btn-success" data-new-confirm data-disable-onloading>Add language</button>
            <button class="btn btn-secondary mar-left-auto" data-dismiss="modal">Close</button>
        @endslot
    @endcomponent
@endsection