<table>
    <thead>

        <tr>
            @if ($isFirstSheet)
                <th style="background-color: yellow; font-weight: bold;">ตัวแปรที่ว่าง</th>
            @endif
            @foreach ($header as $col)
                @if (in_array($col, $highlight_columns))
                    <td style="background-color: yellow">{{ $col }}</td>
                @else
                    <td>{{ $col }}</td>
                @endif
            @endforeach

        </tr>
    </thead>
    <tbody>
        @foreach ($isData as $row)
            <tr>
                @if ($isFirstSheet)
                    <td style="background-color: yellow; font-weight: bold;">
                        {{ isset($emptyFields[$row->id]) ? implode(', ', $emptyFields[$row->id]) : '' }}
                    </td>
                @endif
                @foreach ($header as $col)
                    @if (in_array($col, $highlight_columns))
                        <td style="background-color: yellow">{{ $row->{$col} }}</td>
                    @else
                        <td>{{ $row->{$col} }}</td>
                    @endif
                @endforeach

            </tr>
        @endforeach
    </tbody>
</table>
