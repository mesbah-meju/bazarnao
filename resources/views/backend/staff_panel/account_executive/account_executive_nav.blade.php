<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #A73986;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
        aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item  @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('/staff')}}">Daily Activity</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('/staff_pos_ledger')}}">POS Ledger</a>
            </li>
            <li class="nav-item  @if(str_contains(url()->current(), '/account_activity_report')) {{'active'}} @endif">
                <a class="nav-link" href="{{route('account_activity_report.index')}}">Activity Report</a>
            </li>
            
            <li class="nav-item @if(str_contains(url()->current(), '/operation_sales_report')) {{'active'}} @endif">
            <a class="nav-link" href="{{route('operation_sales_report.index')}}">Sales Report</a>
            </li>

            <li class="nav-item @if(str_contains(url()->current(), '/supplier_ledger')) {{'active'}} @endif">
            <a class="nav-link" href="{{route('supplier_ledger.index')}}">Supplier Ledger</a>
            </li>

        </ul>

    </div>
</nav>