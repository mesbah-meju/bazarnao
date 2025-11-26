<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #A73986;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
        aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('/staff')}}">Daily Activity</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('/staff_pos_ledger')}}">POS Ledger</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staff_customer_ledger')) {{'active'}} @endif">
                <a class="nav-link" href="{{route('staff_customer_ledger')}}">Customer Ledger</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staff_sales_report')) {{'active'}} @endif">
                <a class="nav-link" href="{{route('staff_sales_report')}}">Sales Report</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staff_product_sales_report')) {{'active'}} @endif">
                <a class="nav-link" href="{{route('staff_product_sales_report')}}">Product Sales Report</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staffmycustomerslist')) {{'active'}} @endif">
                <a class="nav-link" href="{{route('staffmycustomerslist')}}">Customers</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/customers_comments_complain')) {{'active'}} @endif">
                <a class="nav-link" href="{{route('customers_comments_complain')}}">Comment/Complain</a>
            </li>
        </ul>

    </div>
</nav>