<table class="table table-bordered">
    <thead>
        <tr>
            <th>Warehouse</th>
            @for ($year = $currentYear; $year >= $startYear; $year--)
                <th>{{ $year }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($productsData as $productData)
            <tr>
                <td>{{ $productData['warehouse_name'] }}</td>
                @for ($year = $currentYear; $year >= $startYear; $year--)
                    <td>{{ number_format($productData['totals'][$year] ?? 0, 2) }}</td>
                @endfor
            </tr>
        @endforeach
        <tr>
            <td><b>Total</b></td>
            @for ($year = $currentYear; $year >= $startYear; $year--)
                @php
                    $yearTotal = 0;
                    foreach ($productsData as $productData) {
                        if (isset($productData['totals'][$year])) {
                            $yearTotal += $productData['totals'][$year];
                        }
                    }
                @endphp
                <td><b>{{ number_format($yearTotal, 2) }}</b></td>
            @endfor
        </tr>
    </tbody>
</table>
