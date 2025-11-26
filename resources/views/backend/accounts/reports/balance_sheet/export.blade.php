<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px;">
                Balance Sheet Report
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 14px;">
                Balance Sheet From {{ date('d-m-Y', strtotime($dtpFromDate)) }} To {{ date('d-m-Y', strtotime($dtpToDate)) }}
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="4"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">{{ Session::get('fyearName') }}</th>
            @foreach($financialyears as $financialyear)
                <th style="font-weight: bold; border: 1px solid #000;">{{ $financialyear }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- ASSETS SECTION -->
        @foreach($assets as $asset)
            <tr>
                <td style="border: 1px solid #000;">{{ $asset['head'] ?? '' }}</td>
                <td colspan="3" style="border: 1px solid #000;"></td>
            </tr>
            @if(isset($asset['nextlevel']) && count($asset['nextlevel']) > 0)
                @foreach ($asset['nextlevel'] as $value)
                    @if(($value['subtotal'] ?? 0) != 0 || ($value['ssubtotal'] ?? 0) != 0 || ($value['tsubtotal'] ?? 0) != 0)
                    <tr>
                        <td style="border: 1px solid #000;">    {{ $value['headName'] ?? '' }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['subtotal'], 2, '.', ',') }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['ssubtotal'], 2, '.', ',') }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['tsubtotal'], 2, '.', ',') }}</td>
                    </tr>
                    @endif
                    @if(isset($value['innerHead']) && count($value['innerHead']) > 0)
                        @foreach($value['innerHead'] as $inner)
                            @if(($inner['amount'] ?? 0) != 0 || ($inner['secondyear'] ?? 0) != 0 || ($inner['thirdyear'] ?? 0) != 0)
                            <tr>
                                <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['amount'], 2, '.', ',') }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['secondyear'], 2, '.', ',') }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['thirdyear'], 2, '.', ',') }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        <tr>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Assets</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($assets[0]['gtotal'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($assets[0]['sgtotal'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($assets[0]['tgtotal'] ?? 0, 2, '.', ',') }}</td>
        </tr>

        <tr style="background-color: #E7E0EE;">
            <td colspan="4" style="border: 1px solid #000;"></td>
        </tr>

        <!-- LIABILITIES SECTION -->
        @foreach($liabilities as $liability)
            <tr>
                <td style="border: 1px solid #000;">{{ $liability['head'] ?? '' }}</td>
                <td colspan="3" style="border: 1px solid #000;"></td>
            </tr>
            @if(isset($liability['nextlevel']) && count($liability['nextlevel']) > 0)
                @foreach ($liability['nextlevel'] as $value)
                    @if(($value['subtotal'] ?? 0) != 0 || ($value['ssubtotal'] ?? 0) != 0 || ($value['tsubtotal'] ?? 0) != 0)
                    <tr>
                        <td style="border: 1px solid #000;">    {{ $value['headName'] ?? '' }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['subtotal'], 2, '.', ',') }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['ssubtotal'], 2, '.', ',') }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['tsubtotal'], 2, '.', ',') }}</td>
                    </tr>
                    @endif
                    @if(isset($value['innerHead']) && count($value['innerHead']) > 0)
                        @foreach($value['innerHead'] as $inner)
                            @if(($inner['amount'] ?? 0) != 0 || ($inner['secondyear'] ?? 0) != 0 || ($inner['thirdyear'] ?? 0) != 0)
                            <tr>
                                <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['amount'], 2, '.', ',') }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['secondyear'], 2, '.', ',') }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['thirdyear'], 2, '.', ',') }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        <tr>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Liabilities</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($liabilities[0]['gtotal'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($liabilities[0]['sgtotal'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($liabilities[0]['tgtotal'] ?? 0, 2, '.', ',') }}</td>
        </tr>

        <tr style="background-color: #E7E0EE;">
            <td colspan="4" style="border: 1px solid #000;"></td>
        </tr>

        <!-- EQUITY SECTION -->
        @foreach($equitys as $equity)
            <tr>
                <td style="border: 1px solid #000;">{{ $equity['head'] ?? '' }}</td>
                <td colspan="3" style="border: 1px solid #000;"></td>
            </tr>
            @if(isset($equity['nextlevel']) && count($equity['nextlevel']) > 0)
                @foreach ($equity['nextlevel'] as $value)
                    @if(($value['subtotal'] ?? 0) != 0 || ($value['ssubtotal'] ?? 0) != 0 || ($value['tsubtotal'] ?? 0) != 0)
                    <tr>
                        <td style="border: 1px solid #000;">    {{ $value['headName'] ?? '' }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['subtotal'], 2, '.', ',') }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['ssubtotal'], 2, '.', ',') }}</td>
                        <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['tsubtotal'], 2, '.', ',') }}</td>
                    </tr>
                    @endif
                    @if(isset($value['innerHead']) && count($value['innerHead']) > 0)
                        @foreach($value['innerHead'] as $inner)
                            @if(($inner['amount'] ?? 0) != 0 || ($inner['secondyear'] ?? 0) != 0 || ($inner['thirdyear'] ?? 0) != 0)
                            <tr>
                                <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['amount'], 2, '.', ',') }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['secondyear'], 2, '.', ',') }}</td>
                                <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['thirdyear'], 2, '.', ',') }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        <tr>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Equity</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($equitys[0]['gtotal'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($equitys[0]['sgtotal'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format($equitys[0]['tgtotal'] ?? 0, 2, '.', ',') }}</td>
        </tr>

        <!-- TOTAL LIABILITIES + EQUITY -->
        <tr style="background-color: #d0d0d0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Liabilities + Equity</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format(($liabilities[0]['gtotal'] ?? 0) + ($equitys[0]['gtotal'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format(($liabilities[0]['sgtotal'] ?? 0) + ($equitys[0]['sgtotal'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency . ' ' . number_format(($liabilities[0]['tgtotal'] ?? 0) + ($equitys[0]['tgtotal'] ?? 0), 2, '.', ',') }}</td>
        </tr>
    </tbody>
</table>

