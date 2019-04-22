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