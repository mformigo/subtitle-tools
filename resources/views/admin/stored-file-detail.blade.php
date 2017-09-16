@extends('admin.layout.admin-template')

@section('content')

    <div id="StoredFileDetail">
        <h1>Stored File Detail</h1>

        <table class="meta">
            <tr>
                <td>Id</td>
                <td>
                    <form target="_blank" method="post" action="{{ route('adminStoredFileDownload') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="id" value="{{ $storedFileId }}" />
                        <button class="plain" type="submit">{{ $storedFileId }}</button>
                    </form>
                </td>
            </tr>
            @if($meta !== null)
                <tr>
                    <td>Size</td>
                    <td>{{ round($meta->size / 1024, 2) }}kb</td>
                </tr>
                <tr>
                    <td>Mime</td>
                    <td>{{ $meta->mime }}</td>
                </tr>
                <tr>
                    <td>Encoding</td>
                    <td>{{ $meta->encoding }}</td>
                </tr>
                <tr>
                    <td>Identified as</td>
                    <td>{{ class_basename($meta->identified_as) }}</td>
                </tr>
                <tr>
                    <td>Line endings</td>
                    <td>{{ $meta->line_endings }}</td>
                </tr>
                <tr>
                    <td>Line count</td>
                    <td>{{ $meta->line_count }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="2">Stored file meta not yet available</td>
                </tr>
            @endif
        </table>


<div class="pres">

<pre>
@foreach($lines as $line)
{{ $loop->iteration }}
@endforeach
</pre>

<pre>
@foreach($lines as $line)
{{ $line }}
@endforeach
</pre>

</div>

    </div>

@endsection
