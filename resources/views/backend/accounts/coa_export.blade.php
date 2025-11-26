<table>
    <thead>
        <tr>
            <th colspan="{{ $maxLevel + 2 }}" style="text-align: center; font-weight: bold; font-size: 16px;">
                Chart of Accounts
            </th>
        </tr>
        <tr>
            <th colspan="{{ $maxLevel + 2 }}" style="text-align: center; font-weight: bold; font-size: 14px;">
                COA Print - {{ date('d-m-Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ $maxLevel + 2 }}"></th>
        </tr>
        <tr>
            @for ($i = 1; $i <= $maxLevel; $i++)
                <th style="font-weight: bold; border: 1px solid #000;">Level {{ $i }}</th>
            @endfor
            <th style="font-weight: bold; border: 1px solid #000;">Head Code</th>
            <th style="font-weight: bold; border: 1px solid #000;">Head Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($coaData as $row)
            @php
                $headLevel = $row->head_level;
                $levelDiff = $maxLevel + 1 - $headLevel;
            @endphp
            <tr>
                <!-- Indentation based on HeadLevel -->
                @for ($j = 0; $j < $headLevel; $j++)
                    <td style="border: 1px solid #000;"></td>
                @endfor
                
                <!-- Display HeadCode and HeadName -->
                <td style="border: 1px solid #000;">{{ $row->head_code }}</td>
                <td colspan="{{ $levelDiff }}" style="border: 1px solid #000;">{{ $row->head_name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

