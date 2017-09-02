@extends('guest.layout.base-template')

@section('title',       __('seo.title.shiftPartial'))
@section('description', __('seo.description.shiftPartial'))
@section('keywords',    __('seo.keywords.shiftPartial'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Partial Subtitle Resync @endslot

        This tools allows you to shift multiple points in a subtitle file.
        The <a href="{{ route('shift') }}">Shifter Tool</a> resyncs the whole file, this tool only resyncs specific parts it.
        The 'to' field has to be later than the 'from' field, shift can be any number except zero. Shift values are in milliseconds.

    @endcomponent


    @component('guest.components.tool-form')

        @slot('title') Select a file to resync @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, zip @endslot

        @slot('buttonText') Shift @endslot

        @slot('extraAfter')
            <table id="multi-shift-table" class="table mw320">
                <thead>
                <tr>
                    <th>From</th><th>To</th><th>Shift</th><th></th>
                </tr>
                </thead>
                <tbody>

                @php
                    $shifts = old('shifts', [['from' => '', 'to' => '', 'milliseconds' => '']]);

                    $shifts = array_map(function($arr) {
                        return (object)$arr;
                    }, $shifts);

                    foreach($shifts as $shift) {
                        $shift->isValid = preg_match('/\d\d:\d\d:\d\d/', $shift->from) &&
                                          preg_match('/\d\d:\d\d:\d\d/', $shift->to)   &&
                                          preg_match('/-?\d+/', $shift->milliseconds)  &&
                                          str_replace(':', '', $shift->to) > str_replace(':', '', $shift->from);

                        $shift->isEmpty = empty($shift->from) && empty($shift->to) && empty($shift->milliseconds);
                    }
                @endphp

                @foreach($shifts as $shift)
                    <tr class="cloneable{{ $shift->isValid || $shift->isEmpty ? '' : ' table-danger bg-fade' }}">
                        <td><input name="shifts[{{ $loop->iteration }}][from]"         value="{{ $shift->from }}" class="time-field" placeholder="hh:mm:ss" title="valid input is HH:MM:SS (23:59:59)" required type="text" pattern="\d\d:\d\d:\d\d"/></td>
                        <td><input name="shifts[{{ $loop->iteration }}][to]"           value="{{ $shift->to }}"   class="time-field" placeholder="hh:mm:ss" title="valid input is HH:MM:SS (23:59:59)" required type="text" pattern="\d\d:\d\d:\d\d"/></td>
                        <td><input name="shifts[{{ $loop->iteration }}][milliseconds]" value="{{ $shift->milliseconds }}"   class="ms-input"   placeholder="1000"     title="shift in milliseconds"              required type="number"></td>
                        <td>
                            <a onclick="deleteRow(this)"  class="btn-floating btn-large waves-effect waves-light red deleter"><i class="material-icons">remove</i></a>
                        </td>
                    </tr>
                @endforeach

                <tr class="cloner-row">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <a class="btn-floating btn-large waves-effect waves-light red cloner"><i class="material-icons">add</i></a>
                    </td>
                </tr>

                </tbody>
            </table>
        @endslot

    @endcomponent


    @push('inline-footer-scripts')
        <script>

            var formInt = {{ count($shifts)  }};

            $("input[type=text]").mask("99:99:99", {placeholder:"-"});

            $("#multi-shift-table a.cloner").on("click", function()
            {
                var newRow = $(".cloneable").last().clone();

                newRow.removeClass("table-danger");
                newRow.removeClass("bg-fade");

                var timeFields = newRow.children().find("input[type=text]");

                timeFields.mask("99:99:99", {placeholder:"-"});

                timeFields.first().val(timeFields.last().val());
                timeFields.last().val("");

                newRow.children().find("input[type=number]").val("");

                formInt++;

                newRow.find("input").each(function(el) {
                    this.name = this.name.replace(/\[\d+\]/, function(str) {
                        return '[' + formInt + ']';
                    });
                });

                $("#multi-shift-table tr.cloner-row").before(newRow);

                timeFields.last().focus();

            });


            function deleteRow(el)
            {
                var parent = $(el).closest("tr");

                if($(".cloneable").length > 1)
                {
                    parent.remove();
                }
                else
                {
                    parent.find("input").val("");
                }
            }

            $("tr.table-danger input").on("focus", function()
            {
                $(this).closest("tr").removeClass("table-danger");
            });
        </script>
    @endpush


@endsection
