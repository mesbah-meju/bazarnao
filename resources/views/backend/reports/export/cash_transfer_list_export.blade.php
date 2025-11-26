<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>From Warehouse</th>
            <th>To Warehouse</th>            
            <th>Amount</th>
            <th>Date</th>
            <th >Approved Date</th>
            <th>Status</th>
            <th >trans ID</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
           
        @endphp

        @foreach ($transfer as $key => $trans)
            @php
                
                $fromWarehouse = \App\Models\Warehouse::find($trans->from_wearhouse_id);
                $toWarehouse = \App\Models\Warehouse::find($trans->to_wearhouse_id);
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $fromWarehouse ? $fromWarehouse->name : 'N/A' }}</td>
                <td>{{ $toWarehouse ? $toWarehouse->name : 'N/A' }}</td>
                <td>{{ $trans->amount ?? '-' }}</td>
                <td>{{ $trans->date }}</td>
                <td>
                    {{ $trans->approved_date }}
                </td>
                <td>{{ $trans->status }}</td>
                
                <td>
                    {{ str_pad($trans->id, 5, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        @endforeach
        
    </tbody>
</table>
