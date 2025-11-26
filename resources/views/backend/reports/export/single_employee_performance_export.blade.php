<table class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="2">Month</th>
            @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                <th colspan="1">{{ $year }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($months as $monthData)
        <tr>
            <td>{{ $monthData['name'] }}</td>
            @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                <td class="text-right">Qty: {{ $monthData[$year]['quantity'] ?? 0 }} <br> {{ single_price($monthData[$year]['grand_total'] ?? 0, 2) }}</td>
            @endfor
        </tr>
        @endforeach
        <tr>
            <td style="text-align:right;" colspan="1"><b>Total</b></td>
            @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                <td style="text-align:right;"><b>Qty: {{ $totals[$year]['quantity'] ?? 0 }} <br> {{ single_price($totals[$year]['grand_total'] ?? 0, 2) }}</b></td>
            @endfor
        </tr>
    </tbody>
</table>
