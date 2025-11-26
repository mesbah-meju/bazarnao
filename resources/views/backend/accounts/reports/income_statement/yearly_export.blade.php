<table>
    <thead>
        <tr>
            <th colspan="{{ count($financial_years) + 1 }}" style="text-align: center; font-weight: bold; font-size: 16px;">
                Income Statement Yearly Report
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($financial_years) + 1 }}" style="text-align: center; font-weight: bold; font-size: 14px;">
                Income Statement for {{ $curentYear->year_name }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($financial_years) + 1 }}" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($financial_years) + 1 }}"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Account Head</th>
            @foreach($financial_years as $year)
                <th style="font-weight: bold; border: 1px solid #000;">{{ $year->year_name }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- INCOME SECTION -->
        <tr>
            <td colspan="{{ count($financial_years) + 1 }}" style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">INCOME</td>
        </tr>
        @if (count($incomes) > 0)
            @foreach ($incomes as $income)
                @if (isset($income['nextlevel']) && count($income['nextlevel']) > 0)
                    @foreach ($income['nextlevel'] as $value)
                        @if (isset($value['innerHead']) && count($value['innerHead']) > 0)
                            @foreach ($value['innerHead'] as $inner)
                                @php
                                    $yearly_incomes = ($inner['amount1'] ?? 0) + ($inner['amount2'] ?? 0) + ($inner['amount3'] ?? 0) + ($inner['amount4'] ?? 0) + ($inner['amount5'] ?? 0) + ($inner['amount6'] ?? 0) + ($inner['amount7'] ?? 0) + ($inner['amount8'] ?? 0) + ($inner['amount9'] ?? 0) + ($inner['amount10'] ?? 0) + ($inner['amount11'] ?? 0) + ($inner['amount12'] ?? 0);
                                @endphp
                                @if($yearly_incomes != 0)
                                <tr>
                                    <td style="border: 1px solid #000;">{{ $inner['headName'] }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($yearly_incomes, 2, '.', ',') }}</td>
                                    @for($j = 1; $j < count($financial_years); $j++)
                                        <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
                                    @endfor
                                </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
            <tr>
                <td style="font-weight: bold; border: 1px solid #000;">Total Income</td>
                @php
                    $totalIncomeYear = ($incomes[0]['gtotal1'] ?? 0) + ($incomes[0]['gtotal2'] ?? 0) + ($incomes[0]['gtotal3'] ?? 0) + ($incomes[0]['gtotal4'] ?? 0) + ($incomes[0]['gtotal5'] ?? 0) + ($incomes[0]['gtotal6'] ?? 0) + ($incomes[0]['gtotal7'] ?? 0) + ($incomes[0]['gtotal8'] ?? 0) + ($incomes[0]['gtotal9'] ?? 0) + ($incomes[0]['gtotal10'] ?? 0) + ($incomes[0]['gtotal11'] ?? 0) + ($incomes[0]['gtotal12'] ?? 0);
                @endphp
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($totalIncomeYear, 2, '.', ',') }}</td>
                @for($j = 1; $j < count($financial_years); $j++)
                    <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
                @endfor
            </tr>
        @endif

        <tr><td colspan="{{ count($financial_years) + 1 }}"></td></tr>

        <!-- COST OF GOODS SOLD SECTION -->
        <tr>
            <td colspan="{{ count($financial_years) + 1 }}" style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">COST OF GOODS SOLD</td>
        </tr>
        @if (count($costofgoodsolds) > 0)
            @foreach ($costofgoodsolds as $cogs)
                @if (isset($cogs['nextlevel']) && count($cogs['nextlevel']) > 0)
                    @foreach ($cogs['nextlevel'] as $value)
                        @if (isset($value['innerHead']) && count($value['innerHead']) > 0)
                            @foreach ($value['innerHead'] as $inner)
                                @php
                                    $yearly_cogs = ($inner['amount1'] ?? 0) + ($inner['amount2'] ?? 0) + ($inner['amount3'] ?? 0) + ($inner['amount4'] ?? 0) + ($inner['amount5'] ?? 0) + ($inner['amount6'] ?? 0) + ($inner['amount7'] ?? 0) + ($inner['amount8'] ?? 0) + ($inner['amount9'] ?? 0) + ($inner['amount10'] ?? 0) + ($inner['amount11'] ?? 0) + ($inner['amount12'] ?? 0);
                                @endphp
                                @if($yearly_cogs != 0)
                                <tr>
                                    <td style="border: 1px solid #000;">{{ $inner['headName'] }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($yearly_cogs, 2, '.', ',') }}</td>
                                    @for($j = 1; $j < count($financial_years); $j++)
                                        <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
                                    @endfor
                                </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
            <tr>
                <td style="font-weight: bold; border: 1px solid #000;">Total Cost of Goods Sold</td>
                @php
                    $totalCOGSYear = ($costofgoodsolds[0]['subtota1'] ?? 0) + ($costofgoodsolds[0]['subtota2'] ?? 0) + ($costofgoodsolds[0]['subtota3'] ?? 0) + ($costofgoodsolds[0]['subtota4'] ?? 0) + ($costofgoodsolds[0]['subtota5'] ?? 0) + ($costofgoodsolds[0]['subtota6'] ?? 0) + ($costofgoodsolds[0]['subtota7'] ?? 0) + ($costofgoodsolds[0]['subtota8'] ?? 0) + ($costofgoodsolds[0]['subtota9'] ?? 0) + ($costofgoodsolds[0]['subtota10'] ?? 0) + ($costofgoodsolds[0]['subtota11'] ?? 0) + ($costofgoodsolds[0]['subtota12'] ?? 0);
                @endphp
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($totalCOGSYear, 2, '.', ',') }}</td>
                @for($j = 1; $j < count($financial_years); $j++)
                    <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
                @endfor
            </tr>
        @endif

        <!-- GROSS PROFIT -->
        <tr>
            <td style="font-weight: bold; background-color: #e8e8e8; border: 1px solid #000;">GROSS PROFIT</td>
            <td style="text-align: right; font-weight: bold; background-color: #e8e8e8; border: 1px solid #000;">{{ number_format($totalIncomeYear - $totalCOGSYear, 2, '.', ',') }}</td>
            @for($j = 1; $j < count($financial_years); $j++)
                <td style="text-align: right; font-weight: bold; background-color: #e8e8e8; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
            @endfor
        </tr>

        <tr><td colspan="{{ count($financial_years) + 1 }}"></td></tr>

        <!-- OPERATING EXPENSES SECTION -->
        <tr>
            <td colspan="{{ count($financial_years) + 1 }}" style="font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">OPERATING EXPENSES</td>
        </tr>
        @if (count($expenses) > 0)
            @foreach ($expenses as $expense)
                @if (isset($expense['nextlevel']) && count($expense['nextlevel']) > 0)
                    @foreach ($expense['nextlevel'] as $value)
                        @if (isset($value['innerHead']) && count($value['innerHead']) > 0)
                            @foreach ($value['innerHead'] as $inner)
                                @php
                                    $yearly_expenses = ($inner['amount1'] ?? 0) + ($inner['amount2'] ?? 0) + ($inner['amount3'] ?? 0) + ($inner['amount4'] ?? 0) + ($inner['amount5'] ?? 0) + ($inner['amount6'] ?? 0) + ($inner['amount7'] ?? 0) + ($inner['amount8'] ?? 0) + ($inner['amount9'] ?? 0) + ($inner['amount10'] ?? 0) + ($inner['amount11'] ?? 0) + ($inner['amount12'] ?? 0);
                                @endphp
                                @if($yearly_expenses != 0)
                                <tr>
                                    <td style="border: 1px solid #000;">{{ $inner['headName'] }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ number_format($yearly_expenses, 2, '.', ',') }}</td>
                                    @for($j = 1; $j < count($financial_years); $j++)
                                        <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
                                    @endfor
                                </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
            <tr>
                <td style="font-weight: bold; border: 1px solid #000;">Total Operating Expenses</td>
                @php
                    $totalExpensesYear = ($expenses[0]['gtotal1'] ?? 0) + ($expenses[0]['gtotal2'] ?? 0) + ($expenses[0]['gtotal3'] ?? 0) + ($expenses[0]['gtotal4'] ?? 0) + ($expenses[0]['gtotal5'] ?? 0) + ($expenses[0]['gtotal6'] ?? 0) + ($expenses[0]['gtotal7'] ?? 0) + ($expenses[0]['gtotal8'] ?? 0) + ($expenses[0]['gtotal9'] ?? 0) + ($expenses[0]['gtotal10'] ?? 0) + ($expenses[0]['gtotal11'] ?? 0) + ($expenses[0]['gtotal12'] ?? 0);
                @endphp
                <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($totalExpensesYear, 2, '.', ',') }}</td>
                @for($j = 1; $j < count($financial_years); $j++)
                    <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
                @endfor
            </tr>
        @endif

        <!-- NET PROFIT -->
        <tr>
            <td style="font-weight: bold; background-color: #d0d0d0; border: 1px solid #000;">NET PROFIT</td>
            <td style="text-align: right; font-weight: bold; background-color: #d0d0d0; border: 1px solid #000;">{{ number_format(($totalIncomeYear - $totalCOGSYear) - $totalExpensesYear, 2, '.', ',') }}</td>
            @for($j = 1; $j < count($financial_years); $j++)
                <td style="text-align: right; font-weight: bold; background-color: #d0d0d0; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
            @endfor
        </tr>
    </tbody>
</table>

