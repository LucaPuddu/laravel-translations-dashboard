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
    <div class="page">
        <div class="row">
            <div class="col-12 mar-bottom-10">
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
            <div class="col-12 mar-bottom-30">
                <form action="{{route('languages-page', $page->name)}}" method="GET">
                    @if(isset(request()->origin))
                        <input type="hidden" name="origin" value="{{request()->origin}}">
                    @endif
                    @if(isset(request()->destination))
                        <input type="hidden" name="destination" value="{{request()->destination}}">
                    @endif

                    @include('laravel-translations-dashboard::components.search-bar', [
                                'text' => 'Search element in page...'
                            ])
                </form>
            </div>

            <div class="col-12 form mar-bottom-20">
                @include('laravel-translations-dashboard::components.change-language')
            </div>
            <div class="col-12 form">
                <form action="{{route('languages-page-edit')}}" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-12">
                            @if($page->items->count())
                                @include('laravel-translations-dashboard::components.page', [
                                    'page' => $page,
                                    'from_language' => $origin_language->name,
                                    'to_language' => $destination_language->name,
                                ])
                            @else
                                <p><em>No matches.</em></p>
                            @endif
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
    @include('laravel-translations-dashboard::components.delete-element-modal')

    {{--ADD NEW MODAL--}}
    @include('laravel-translations-dashboard::components.add-element-modal')
@endsection