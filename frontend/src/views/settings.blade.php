@extends('laravel-translations-dashboard::layout.base')

@section('page', 'home')

@section('content')
    <div class="settings">
        <h1 class="h3 mar-bottom-20">Global settings</h1>
        <form class="form" action="{{route('languages-settings-edit')}}" method="POST">
            @csrf
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="rich_editor" name="rich_editor" value="1" @if($rich_editor) checked @endif>
                <label class="form-check-label" for="rich_editor">Enable Rich Editor for page elements (use with caution!)</label>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection

