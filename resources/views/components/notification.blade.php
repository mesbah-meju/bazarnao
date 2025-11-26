@props(['notifications', 'is_linkable' => false])


@forelse($notifications as $notification)
    <li class="list-group-item d-flex justify-content-between align-items- py-3">
        <div class="media text-inherit">
            <div class="media-body">
                <p class="mb-1 text-truncate-2">
                    @php $user_type = auth()->user()->user_type; @endphp

                    @if ($notification->type == 'App\Notifications\OrderNotification')
                        {{ translate('Order code: ') }}
                        @if ($is_linkable)
                            @php
                                if ($user_type == 'admin'){
                                    $route = route('all_orders.show', encrypt($notification->data['order_id']));
                                }
                               
                            @endphp
                            <a href="{{ $route }}">
                        @endif

                        {{ $notification->data['order_code'] }}

                        @if ($is_linkable)
                            </a>
                        @endif
                        
                        {{ translate(' has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                        
                    @elseif ($notification->type == 'App\Notifications\ShopVerificationNotification')
                        @if ($user_type == 'admin')

                            {{ $notification->data['name'] }}: 
                            @if ($is_linkable)
                                </a>
                            @endif
                        @else
                            {{ translate('Your ') }}
                        @endif
                        {{ translate('verification request has been '.$notification->data['status']) }}
                    @elseif ($notification->type == 'App\Notifications\ShopProductNotification')
                        {{ translate('Product : ') }}
                        @if ($is_linkable)
                            <a href="{{ $route }}">{{ $product_name }}</a>
                        @else
                            {{ $product_name }}
                        @endif
                        
                        {{ translate(' is').' '.$notification->data['status'] }}
                    @elseif ($notification->type == 'App\Notifications\PayoutNotification')
                       

                         {{ $user_type == 'admin' ? $notification->data['name'].': ' : translate('Your') }}
                         @if ($is_linkable )
                             <a href="{{ $route }}">{{ translate('payment') }}</a>
                         @else
                             {{ translate('payment') }}
                         @endif
                         {{ single_price($notification->data['payment_amount']).' '.translate('is').' '.translate($notification->data['status']) }}
                    @endif
                </p>
                <small class="text-muted">
                    {{ date('F j Y, g:i a', strtotime($notification->created_at)) }}
                </small>
            </div>
        </div>
    </li>
@empty
    <li class="list-group-item">
        <div class="py-4 text-center fs-16">
            {{ translate('No notification found') }}
        </div>
    </li>
@endforelse
