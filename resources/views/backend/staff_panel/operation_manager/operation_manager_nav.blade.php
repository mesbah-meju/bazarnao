<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #A73986;">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
                        aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('staff.dashboard')}}">Daily Activity</a>
                            </li>

                            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('scan-online-order')}}">Online Order Scan</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/damage')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('damage.index')}}">Damage Entry</a>
                            </li>

                            <li class="nav-item @if(str_contains(url()->current(), '/operation_manager_order')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('operation_manager_order.index')}}">Order</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/operation_manager_stock_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('operation_manager_stock_report.index')}}">Stock Report</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                            <a class="nav-link" href="{{url('/staff_refund')}}">Refund/Return</a>
                             </li>
                             <li class="nav-item @if(str_contains(url()->current(), '/operation_sales_report')) {{'active'}} @endif">
                            <a class="nav-link" href="{{route('staff_sales_report')}}">Sales Report</a>
                           </li>
                        </ul>

                    </div>
                </nav>