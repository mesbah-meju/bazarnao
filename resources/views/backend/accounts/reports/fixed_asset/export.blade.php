<table>
    <thead>
        <tr>
            <th colspan="12" style="text-align: center; font-weight: bold; font-size: 16px;">
                Fixed Assets Annual Report
            </th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center; font-weight: bold; font-size: 14px;">
                Fixed Assets Annual Report {{ $currentYear->year_name }}
            </th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Opening Balance of Fixed Assets</th>
            <th style="font-weight: bold; border: 1px solid #000;">Additions</th>
            <th style="font-weight: bold; border: 1px solid #000;">Adjustment</th>
            <th style="font-weight: bold; border: 1px solid #000;">Closing Balance of Fixed Assets</th>
            <th style="font-weight: bold; border: 1px solid #000;">Depreciation Rate</th>
            <th style="font-weight: bold; border: 1px solid #000;">Depreciation Value</th>
            <th style="font-weight: bold; border: 1px solid #000;">Opening Balance of Accumulated Depreciation</th>
            <th style="font-weight: bold; border: 1px solid #000;">Additions</th>
            <th style="font-weight: bold; border: 1px solid #000;">Adjustment</th>
            <th style="font-weight: bold; border: 1px solid #000;">Closing Balance of Accumulated Depreciation</th>
            <th style="font-weight: bold; border: 1px solid #000;">Written Down Value</th>
        </tr>
    </thead>
    <tbody>
        @if(count($fixedAssets) > 0)
            @foreach($fixedAssets as $fixedAsset)
                <tr>
                    <td style="border: 1px solid #000;">{{ $fixedAsset['headName'] ?? '' }}</td>
                    <td colspan="11" style="border: 1px solid #000;"></td>
                </tr>
                @if(isset($fixedAsset['nextlevel']) && count($fixedAsset['nextlevel']) > 0)
                    @foreach ($fixedAsset['nextlevel'] as $value)
                        <tr>
                            <td style="border: 1px solid #000;">    {{ $value['headName'] ?? '' }}</td>
                            <td colspan="11" style="border: 1px solid #000;"></td>
                        </tr>
                        @if(isset($value['innerHead']) && count($value['innerHead']) > 0)
                            @foreach($value['innerHead'] as $inner)
                                <tr>
                                    <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['openig'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['curentDebit'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['curentCredit'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['curentValue'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ ($inner['depRate'] ?? 0) . ' %' }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['depAmount'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['revOpening'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['revCredit'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['revDebit'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['revBalance'] ?? 0, 2, '.', ',') }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['famount'] ?? 0, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach

            <tr style="background-color: #f0f0f0;">
                <td style="font-weight: bold; border: 1px solid #000;">Total</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal1'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal2'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal3'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal4'] ?? 0, 2, '.', ',') }}</td>
                <td style="border: 1px solid #000;"></td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal5'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal6'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal7'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal8'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal9'] ?? 0, 2, '.', ',') }}</td>
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($fixedAssets[0]['subtotal10'] ?? 0, 2, '.', ',') }}</td>
            </tr>
        @endif
    </tbody>
</table>

