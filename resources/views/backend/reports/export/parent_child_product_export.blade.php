<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>Parent Product Name</th>
            <th>Child Product Name</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach ($parents as $parent)
            @php $childProducts = $children->get($parent->id); @endphp
            @if ($childProducts && $childProducts->count() > 0)
                <tr class="parent-row">
                    <td>{{ $i++ }}</td>
                    <td colspan="1"><strong>{{ $parent->name }}</strong></td>
                    <td></td>
                    <td>{{ number_format($parent->unit_price, 2) }}</td>
                </tr>
                @foreach ($childProducts as $child)
                    <tr class="child-row">
                        <td></td>
                        <td></td>
                        <td>{{ $child->name }}</td>
                        <td>{{ number_format($child->unit_price, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
