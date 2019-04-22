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