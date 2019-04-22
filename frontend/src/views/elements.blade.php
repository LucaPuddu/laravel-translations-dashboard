@extends('laravel-translations-dashboard::layout.base')

@section('page', 'elements')

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
    <div class="elements" data-save-action="{{route('languages-element-edit')}}">
        <div class="row">
            <div class="col-12 mar-bottom-10">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success d-none element-deleted" role="alert">
                            Element successfully deleted.
                        </div>
                        <div class="alert alert-danger d-none save-error" role="alert">
                            Error with saving element.
                        </div>
                        <div class="alert alert-success d-none element-new" role="alert">
                            Element successfully created.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mar-bottom-30">
                <form action="{{route('languages-elements')}}" method="GET">
                    @if(isset(request()->origin))
                        <input type="hidden" name="origin" value="{{request()->origin}}">
                    @endif
                    @if(isset(request()->destination))
                        <input type="hidden" name="destination" value="{{request()->destination}}">
                    @endif

                    @include('laravel-translations-dashboard::components.search-bar', [
                                'text' => 'Search element...'
                            ])
                </form>
            </div>
            <div class="col-12 form mar-bottom-20">
                @include('laravel-translations-dashboard::components.change-language')
            </div>
            <div class="col-12 form">
                <div class="row">
                    <div class="col-12" id="elements-container">
                        @if($pages->count())
                            @foreach($pages as $pageName => $page)
                                <div class="mar-bottom-20">
                                    @component('laravel-translations-dashboard::components.card')
                                        @include('laravel-translations-dashboard::components.page', [
                                            'page' => $page,
                                            'from_language' => $origin_language->name,
                                            'to_language' => $destination_language->name,
                                        ])
                                    @endcomponent
                                </div>
                            @endforeach
                        @else
                            <p><em>No matches.</em></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--DELETE MODAL--}}
    @include('laravel-translations-dashboard::components.delete-element-modal')

    {{--ADD NEW MODAL--}}
    @include('laravel-translations-dashboard::components.add-element-modal')
@endsection