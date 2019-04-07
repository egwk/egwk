<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Comparing {{  implode(', ', $books) }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        th, td {
            vertical-align: top;
        }
    </style>

</head>
<body>
@if($error && isset($message))
    <div>
        <p>{{ $message }}</p>
    </div>
@elseif(!$data)
    <div>
        <p>Job has started.</p>
        <p>Compiling {{ implode(', ', $books) }}</p>
    </div>
@else
    <table class="table">
        <thead>
        <tr>
            @foreach($books as $book)
                <th scope="col">{{ $book }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>

        @foreach($data as $row)
            <tr>
                <td>
                    {!! '<' . array_get($row, "self.element_type", '') . ' class="' . array_get($row, "self.element_subtype", '') . '" >' !!}
                    {!! array_get($row, "self.content") !!}
                    {!! '</' . array_get($row, "self.element_type", '') . '>' !!}
                    <span class="paragraph-code">{{ '{' . array_get($row, "self.refcode_short", '') . '}'}}
                    [ {{ array_get($row, "self.para_id", '') }}, {{ array_get($row, "self.puborder", '') }}.  ]</span>
                </td>
                @foreach($books as $book)
                    @if (!$loop->first)
                        <td>
                            @if(array_get($row, "similars.$book.paragraph.content", '') !== '')
                                {!! '<' . array_get($row, "similars.$book.paragraph.element_type", '') . ' class="' . array_get($row, "similars.$book.paragraph.element_subtype", '') . '" >' !!}
                                {!! array_get($row, "similars.$book.paragraph.content") !!}
                                {!! '</' . array_get($row, "similars.$book.paragraph.element_type", '') . '>' !!}
                                <span class="paragraph-code">{{ '{' . array_get($row, "similars.$book.paragraph.refcode_short", '') . '}' }}
                                [ {{ array_get($row, "similars.$book.paragraph.para_id", '') }}, {{ array_get($row, "similars.$book.paragraph.puborder", '') }}.  ]</span>
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach

        </tbody>
    </table>

@endif

</body>
