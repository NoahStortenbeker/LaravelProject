@extends('layouts.app')

@section('title', 'Order confirmation - FootLab')

@section('content')
    <section class="products_page">
        <div class="products_wrapper">
            <div class="products_breadcrumbs">
                <a href="{{ route('products.index') }}" class="bc_link">PRODUCTS</a>
                <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                <span class="bc_current">CONFIRMATION</span>
            </div>

            <h2 class="admin_list_title" style="margin-bottom: 24px;">Order confirmed</h2>

            <div class="product_form">
                @php
                    $rawStatus = strtolower((string) ($order->status ?? ''));
                    $statusKey = match ($rawStatus) {
                        'completed' => 'completed',
                        'canceling' => 'canceling',
                        'cancelled', 'canceled' => 'canceled',
                        'paid', 'processing' => 'processing',
                        'pending', 'pending_payment' => 'pending',
                        default => 'pending',
                    };
                    $statusLabel = match ($statusKey) {
                        'completed' => 'Completed',
                        'canceling' => 'Canceling',
                        'canceled' => 'Canceled',
                        'processing' => 'Processing',
                        default => 'Pending',
                    };
                    $etaFrom = now()->addDays(2);
                    $etaTo = now()->addDays(5);
                @endphp

                <div class="checkout_layout">
                    <div class="checkout_left">
                        <div class="checkout_card">
                            <div class="checkout_card_title">Order #{{ $order->id }}</div>

                            <div class="confirmation_meta">
                                <div class="confirmation_row">
                                    <span class="confirmation_label">Status</span>
                                    <span class="status_badge status_badge--{{ $statusKey }}">{{ $statusLabel }}</span>
                                </div>
                                <div class="confirmation_row">
                                    <span class="confirmation_label">Order date</span>
                                    <span class="confirmation_value">{{ optional($order->created_at)->format('d / m / Y') }}</span>
                                </div>
                                <div class="confirmation_row">
                                    <span class="confirmation_label">Estimated delivery</span>
                                    <span class="confirmation_value">{{ $etaFrom->format('D d M') }} – {{ $etaTo->format('D d M') }}</span>
                                </div>
                            </div>

                            <div class="confirmation_section">
                                <div class="confirmation_section_title">Shipping</div>
                                <div class="confirmation_text">{{ $order->shipping_name }}</div>
                                <div class="confirmation_text">{{ $order->shipping_address_line }}</div>
                                <div class="confirmation_text">{{ $order->shipping_zip_code }} {{ $order->shipping_city }}</div>
                                <div class="confirmation_text">{{ $order->shipping_country }}</div>
                                @if($order->shipping_email)
                                    <div class="confirmation_text">{{ $order->shipping_email }}</div>
                                @endif
                                @if($order->shipping_phone)
                                    <div class="confirmation_text">{{ $order->shipping_phone }}</div>
                                @endif
                            </div>

                            <div class="product_actions confirmation_actions">
                                <a class="vacancy_create_btn" href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">SHOP AGAIN</a>
                                <a class="vacancy_create_btn" href="{{ route('users.dashboard', ['panel' => 'orders']) }}" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">ORDER HISTORY</a>
                            </div>
                        </div>
                    </div>

                    <aside class="checkout_right">
                        <div class="checkout_card">
                            <div class="checkout_card_title">What you ordered</div>

                            <div class="checkout_items">
                                @foreach($order->items as $item)
                                    <div class="checkout_item">
                                        <div class="checkout_item_img">
                                            @if($item->image)
                                                <img src="{{ $item->image }}" alt="{{ $item->name }}">
                                            @endif
                                        </div>
                                        <div class="checkout_item_info">
                                            <div class="checkout_item_name">{{ $item->name }}</div>
                                            <div class="checkout_item_meta">{{ $item->qty }}x</div>
                                        </div>
                                        <div class="checkout_item_price">€ {{ number_format((float)$item->line_total, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="checkout_totals">
                                <div class="checkout_total_row">
                                    <span>Subtotal</span>
                                    <span>€ {{ number_format((float)($order->subtotal ?? $order->total), 2) }}</span>
                                </div>
                                <div class="checkout_total_row checkout_total_row--grand">
                                    <span>Total</span>
                                    <span>€ {{ number_format((float)$order->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <script>
        try {
            localStorage.removeItem('cart');
        } catch (_) {}
    </script>
@endsection
