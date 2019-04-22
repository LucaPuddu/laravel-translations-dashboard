<form action="{{url()->current()}}" method="GET"
      id="language-form">
    <input type="hidden" name="search" value="{{request()->search}}">

    <div class="row margin-bottom-md align-items-center">
        <div class="col">
            <strong>From:</strong><br>
            <select class="form-control" name="origin">
                @foreach($languages as $language)
                    <option value="{{$language->locale}}"
                            @if($origin_language->locale === $language->locale) selected @endif>{{$language->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <strong>To:</strong><br>
            <select class="form-control" name="destination">
                @foreach($languages as $language)
                    <option value="{{$language->locale}}"
                            @if($destination_language->locale === $language->locale) selected @endif>{{$language->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>