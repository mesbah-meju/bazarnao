
                <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #A73986;">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
                        aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                            <li class="nav-item active @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                            <a class="nav-link" href="{{url('staff')}}">Daily Activity</a>
                            </li>
                         
                            <li class="nav-item @if(str_contains(url()->current(), '/purchase_list_for_purchase_executive')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('purchase_list_for_purchase_executive.index')}}">Purchase</a>
                            </li>

                            <li class="nav-item @if(str_contains(url()->current(), '/transfer_list')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('transfer_list.index')}}">Transfer</a>
                            </li>

                            <li class="nav-item @if(str_contains(url()->current(), '/vendor_create.index')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('vendor_create.index')}}">Create Vendor</a>
                            </li>

                            <li class="nav-item @if(str_contains(url()->current(), '/supplier_ledger_for_purchase_executive')) {{'active'}} @endif"">
                                <a class="nav-link" href="{{route('supplier_ledger_for_purchase_executive.index')}}">Supplier Ledger</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/operation_manager_stock_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('operation_manager_stock_report.index')}}">Stock Report</a>
                            </li>
                            <!-- <li class="nav-item @if(str_contains(url()->current(), '/damage_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('damage_report.index')}}">Damage Report</a>
                            </li> -->

                            <!-- <li class="nav-item @if(str_contains(url()->current(), '/product_wise_purchase_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('product_wise_purchase_report.index')}}">Product Wise Purchase Report</a>
                            </li> -->

                        </ul>

                    </div>
                </nav>