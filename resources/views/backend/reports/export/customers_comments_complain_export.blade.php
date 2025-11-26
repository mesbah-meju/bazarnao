<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>Date</th>
            <th>Executive Name</th>
            <th>Order ID</th>
            <th>Order No</th>
            <th>Customer Name</th>
            <th>Customer Id</th>
            <th>Comment</th>
            <th>Complain</th>
            <th>Total Order</th>
        </tr>
    </thead>
    <tbody>
        @foreach($comment_complain as $key => $comment_com)
            @php
            $executive_name = \App\Models\User::find($comment_com->user_id);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $comment_com->created_at }}</td>
                <td>{{ $executive_name ? $executive_name->name : '' }}</td>
                {{-- <td>{{($comment_com->user_id) }}</td> --}}
                <td>{{ $comment_com->order_id }}</td>
                <td>{{ $comment_com->order_no }}</td>
                <td>{{ $comment_com->name }}</td>
                <td>{{ $comment_com->customer_id }}</td>
                <td>{{ $comment_com->comment }}</td>
                <td>{{ $comment_com->complain }}</td>
                <td>{{ ($comment_com->customer_id) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
