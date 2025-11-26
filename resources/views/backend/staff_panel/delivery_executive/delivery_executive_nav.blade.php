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
            <li class="nav-item @if(str_contains(url()->current(), '/staff_delivery_report')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('/staff_delivery_report')}}">Delivery Report</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/staff_refund')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('/staff_refund')}}">Refund/Return</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/delivery_executive_ledger')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('delivery_executive_ledger')}}">Daily Activity Ledger</a>
            </li>

            <li class="nav-item @if(str_contains(url()->current(), '/delivery_executive_collection_payment')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('delivery_executive_collection_payment')}}">Delivery Ledger</a>
            </li>
            <li class="nav-item @if(str_contains(url()->current(), '/delivery_executive_due_collection')) {{'active'}} @endif">
                <a class="nav-link" href="{{url('delivery_executive_due_collection')}}">Due Collection</a>
            </li>

        </ul>

    </div>
</nav>