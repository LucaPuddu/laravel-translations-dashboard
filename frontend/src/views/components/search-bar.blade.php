<div class="input-group search-bar">
    <div class="input-group-prepend">
        <button class="btn btn-outline-secondary" type="submit" id="search"><i class="icon-search"></i> Search</button>
    </div>
    <input type="text" class="form-control"
           name="search"
           placeholder="{{$text ?? 'Search element, page or language...'}}"
           aria-label="{{$text ?? 'Search element, page or language...'}}"
           value="{{request()->search}}"
           aria-describedby="search">
</div>
@if($errors->any())
    <small class="text-danger">
        {{$errors->first('search')}}
    </small>
@endif