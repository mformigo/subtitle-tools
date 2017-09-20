<form method="post" action="{{ route($route) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <button type="submit" class="plain">{{ $text }}</button>
</form>