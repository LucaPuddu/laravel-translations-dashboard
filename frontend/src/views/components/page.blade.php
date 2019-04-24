<div class="page-elements" data-save-action="{{route('languages-element-edit')}}" data-page="{{$page->name}}">
    <div class="row mar-bottom-15">
        <div class="col-12 col-lg-6">
            <div class="d-inline-block mar-right-30">
                <h1 class="h3"><span class="pre">{{$page->name}}</span> page translations</h1>
            </div>
        </div>

        @can('manage-pages')
            <div class="col-12 col-lg-4">
                <button type="button" class="btn btn-primary float-right add-new-btn" data-toggle="modal" data-target="#add-new">Add new element
                    to <strong>{{$page->name}}</strong></button>
            </div>
        @endcan
    </div>

    <div class="row mar-bottom-15">
        <div class="col-2">
            <strong>Element</strong>
        </div>
        <div class="col-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span>{{$from_language}}</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="row align-items-center justify-content-end">
                <div class="col-auto">
                    <span>{{$to_language}}</span>
                </div>
            </div>
        </div>
        <div class="col-2">
            <strong>Actions</strong>
        </div>
    </div>

    @foreach($page->items as $item)
        <div class="row align-items-stretch mar-bottom-15" data-item="{{$item}}">
            <div class="col-2">
                <span class="pre h6">{{$item}}</span>
            </div>
            <div class="col-4">
                <div class="form-control origin-text">
                    {!! isset($page->origin_translations[$item]) ? $page->origin_translations[$item]->text : '' !!}
                </div>
            </div>
            <div class="col-4">
                @if($rich_editor)
                    <div data-pell>
                        <span data-content>{{isset($page->destination_translations[$item]) ? $page->destination_translations[$item]->text : ''}}</span>
                    </div>
                @else
                    <textarea data-content
                              class="form-control">{{isset($page->destination_translations[$item]) ? $page->destination_translations[$item]->text : ''}}</textarea>
                @endif
                <div class="loader"></div>
            </div>
            <div class="col-2 gap-3">
                <div class="d-inline-block">
                    <button class="btn btn-primary" type="button"
                            data-save-item="{{$item}}">
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