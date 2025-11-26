<table>
    <thead>
        <tr>
            <th>Month</th>
            @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                <th colspan="1">{{ $year }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($productsData as $productData)
            <tr>
                <td colspan="4"><b>Warehouse: {{ $productData['warehouse_name'] }}</b></td>
            </tr>
            @foreach($productData['months'] as $monthData)
                <tr>
                    <td>{{ $monthData['name'] }}</td>
                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                        <td>{{ ($monthData[$year]['amount']) }}</td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><b>Total</b></td>
                @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                    <td><b>{{ ($productData['totals'][$year]['amount']) }}</b></td>
                @endfor
            </tr>
        @endforeach
    </tbody>
</table>
