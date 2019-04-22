@extends('laravel-translations-dashboard::layout.base')

@section('page', 'home')

@section('content')
    <div class="home">
        <div class="row gap-20">
            <div class="col-12 col-sm-6 col-xl-3">
                @component('laravel-translations-dashboard::components.home-card', [
                    'color' => 'blue',
                    'url' => route('languages-languages')
                ])
                    @slot('title')
                        Languages
                    @endslot
                    @slot('quantity')
                        {{$languages}}
                    @endslot
                    @if($languages)
                        @slot('footer')
                            <a class="text-muted text-uppercase" href="{{route('languages-languages')}}">(view all)</a>
                        @endslot
                    @endif
                @endcomponent
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                @component('laravel-translations-dashboard::components.home-card', [
                    'color' => 'green',
                    'icon' => 'folder-open',
                    'url' => route('languages-pages')
                ])
                    @slot('title')
                        Pages
                    @endslot
                    @slot('quantity')
                        {{$pages}}
                    @endslot
                    @if($pages)
                        @slot('footer')
                            <a class="text-muted text-uppercase" href="{{route('languages-pages')}}">(view all)</a>
                        @endslot
                    @endif
                @endcomponent
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                @component('laravel-translations-dashboard::components.card')
                    @component('laravel-translations-dashboard::components.progress', [
                        'quantity' => $translated_translations,
                        'description' => 'Translated elements',
                        'percent' => $progress,
                        'percent_formatted' => $progress_formatted,
                    ])
                    @endcomponent
                @endcomponent
            </div>
        </div>
    </div>
@endsection

