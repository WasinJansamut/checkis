
    <table>
        <thead>

            <tr>
                @foreach($header as $col)
                    @if(in_array($col,$highlight_columns))
                         <td style="background-color: yellow">{{ $col }}</td>
                    @else
                        <td>{{ $col }}</td>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($isData as $row)
            <tr>
                @foreach($header as $col)
                    @if(in_array($col,$highlight_columns))
                        <td style="background-color: yellow">{{ $row->{$col} }}</td>
                    @else
                        <td>{{ $row->{$col} }}</td>
                        @endif

                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>

