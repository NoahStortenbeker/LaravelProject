@extends('layouts.app')

@section('title', 'Checkout - FootLab')

@section('content')
    <section class="products_page">
        <div class="products_wrapper">
            <div class="products_breadcrumbs">
                <a href="{{ route('products.index') }}" class="bc_link">PRODUCTS</a>
                <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                <span class="bc_current">CHECKOUT</span>
            </div>

            <h2 class="admin_list_title" style="margin-bottom: 24px;">Checkout</h2>

            @if($errors->any())
                <div class="toast_top open" style="position: static; transform: none; opacity: 1; pointer-events: auto; margin: 16px 0;">
                    <span class="toast_text">
                        {{ $errors->first() }}
                    </span>
                </div>
            @endif

            <form class="product_form" id="checkoutForm" method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <input type="hidden" name="cart_json" id="cartJsonInput" value="{{ old('cart_json', '') }}">

                <div class="checkout_layout">
                    <div class="checkout_left">
                        <div class="checkout_card">
                            <div class="checkout_card_title">Shipping details</div>

                            <div class="product_grid product_grid--two">
                                <div class="form_group">
                                    <label>Name</label>
                                    <input name="shipping_name" autocomplete="name" value="{{ old('shipping_name', $user->name ?? '') }}">
                                </div>
                                <div class="form_group">
                                    <label>Email</label>
                                    <input name="shipping_email" type="email" autocomplete="email" value="{{ old('shipping_email', $user->email ?? '') }}">
                                </div>
                            </div>

                            <div class="product_grid product_grid--two">
                                <div class="form_group">
                                    <label>Phone</label>
                                    <input name="shipping_phone" autocomplete="tel" value="{{ old('shipping_phone', $user->phone_number ?? '') }}">
                                </div>
                                <div class="form_group">
                                    <label>Country</label>
                                    <input name="shipping_country" autocomplete="country-name" value="{{ old('shipping_country', $user->country ?? '') }}">
                                </div>
                            </div>

                            <div class="product_grid product_grid--two">
                                <div class="form_group">
                                    <label>Address</label>
                                    <input name="shipping_address_line" autocomplete="street-address" value="{{ old('shipping_address_line', $user->address_line ?? '') }}">
                                </div>
                                <div class="form_group">
                                    <label>Zip code</label>
                                    <input name="shipping_zip_code" autocomplete="postal-code" value="{{ old('shipping_zip_code', $user->zip_code ?? '') }}">
                                </div>
                            </div>

                            <div class="product_grid product_grid--two">
                                <div class="form_group">
                                    <label>City</label>
                                    <input name="shipping_city" autocomplete="address-level2" value="{{ old('shipping_city', $user->city ?? '') }}">
                                </div>
                                <div class="form_group">
                                    <label>Notes</label>
                                    <input name="notes" value="{{ old('notes', '') }}" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="checkout_right">
                        <div class="checkout_card">
                            <div class="checkout_card_title">Order summary</div>
                            <div id="checkoutItems" class="checkout_items"></div>

                            <div class="checkout_totals">
                                <div class="checkout_total_row">
                                    <span>Subtotal</span>
                                    <span id="checkoutSubtotal">€ 0,00</span>
                                </div>
                                <div class="checkout_total_row">
                                    <span>Shipping</span>
                                    <span id="checkoutShipping">€ 0,00</span>
                                </div>
                                <div class="checkout_total_row checkout_total_row--grand">
                                    <span>Total</span>
                                    <span id="checkoutTotal">€ 0,00</span>
                                </div>
                            </div>

                            <div id="checkoutEmpty" class="checkout_empty">Your cart is empty.</div>

                            <button type="submit" class="vacancy_create_btn checkout_pay_btn" id="checkoutSubmitBtn">CONTINUE TO PAYMENT</button>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </section>

    <script>
        (function () {
            function readCart() {
                try {
                    const raw = localStorage.getItem('cart');
                    const arr = raw ? JSON.parse(raw) : [];
                    return Array.isArray(arr) ? arr : [];
                } catch (_) { return []; }
            }

            function formatEUR(n) {
                const num = Number(n || 0);
                return '€ ' + num.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            const itemsEl = document.getElementById('checkoutItems');
            const empty = document.getElementById('checkoutEmpty');
            const cartInput = document.getElementById('cartJsonInput');
            const submitBtn = document.getElementById('checkoutSubmitBtn');
            const subtotalEl = document.getElementById('checkoutSubtotal');
            const shippingEl = document.getElementById('checkoutShipping');
            const totalEl = document.getElementById('checkoutTotal');

            function safeText(value) {
                return (value ?? '').toString();
            }

            function clearElement(el) {
                while (el && el.firstChild) el.removeChild(el.firstChild);
            }

            function renderItem(row) {
                const wrap = document.createElement('div');
                wrap.className = 'checkout_item';

                const imgWrap = document.createElement('div');
                imgWrap.className = 'checkout_item_img';
                const img = document.createElement('img');
                img.alt = safeText(row.name || 'Product');

                const src = safeText(row.image || '');
                if (src) {
                    img.src = src;
                    img.loading = 'lazy';
                    imgWrap.appendChild(img);
                } else {
                    const icon = document.createElement('i');
                    icon.className = 'ri-image-line';
                    icon.style.fontSize = '22px';
                    icon.style.opacity = '0.6';
                    imgWrap.appendChild(icon);
                }

                const info = document.createElement('div');
                info.className = 'checkout_item_info';
                const name = document.createElement('div');
                name.className = 'checkout_item_name';
                name.textContent = safeText(row.name || '');
                const meta = document.createElement('div');
                meta.className = 'checkout_item_meta';
                const qty = Number(row.qty || 0);
                const unit = Number(row.price || 0);
                meta.textContent = `${qty}x • ${formatEUR(unit)}`;

                info.appendChild(name);
                info.appendChild(meta);

                const price = document.createElement('div');
                price.className = 'checkout_item_price';
                price.textContent = formatEUR(unit * qty);

                wrap.appendChild(imgWrap);
                wrap.appendChild(info);
                wrap.appendChild(price);

                return wrap;
            }

            function render() {
                const cart = readCart().filter(x => x && x.id && Number(x.qty || 0) > 0);
                const payload = cart.map(x => ({ id: String(x.id), qty: Number(x.qty || 0) }));
                if (cartInput) cartInput.value = JSON.stringify(payload);

                if (!cart.length) {
                    clearElement(itemsEl);
                    if (empty) empty.style.display = 'block';
                    if (submitBtn) submitBtn.disabled = true;
                    if (subtotalEl) subtotalEl.textContent = formatEUR(0);
                    if (shippingEl) shippingEl.textContent = formatEUR(0);
                    if (totalEl) totalEl.textContent = formatEUR(0);
                    return;
                }

                if (empty) empty.style.display = 'none';
                if (submitBtn) submitBtn.disabled = false;

                clearElement(itemsEl);
                cart.forEach((row) => {
                    if (!itemsEl) return;
                    itemsEl.appendChild(renderItem(row));
                });

                const subtotal = cart.reduce((acc, x) => acc + Number(x.price || 0) * Number(x.qty || 0), 0);
                const shipping = 0;
                const total = subtotal + shipping;

                if (subtotalEl) subtotalEl.textContent = formatEUR(subtotal);
                if (shippingEl) shippingEl.textContent = formatEUR(shipping);
                if (totalEl) totalEl.textContent = formatEUR(total);
            }

            render();
            window.addEventListener('storage', (e) => {
                if (e.key === 'cart') render();
            });
        })();
    </script>
@endsection
