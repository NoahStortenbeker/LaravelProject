@extends('layouts.app')

@section('title', 'Payment - FootLab')

@section('content')
    <section class="products_page">
        <div class="products_wrapper">
            <div class="products_breadcrumbs">
                <a href="{{ route('products.index') }}" class="bc_link">PRODUCTS</a>
                <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                <span class="bc_current">PAYMENT</span>
            </div>

            <h2 class="admin_list_title" style="margin-bottom: 24px;">Payment</h2>

            <div class="product_form">
                <form method="POST" action="{{ route('checkout.pay', ['order' => $order->id]) }}">
                    @csrf

                    <div class="checkout_layout">
                        <div class="checkout_left">
                            <div class="checkout_card">
                                <div class="checkout_card_title">Payment options</div>

                                <div class="payment_accordion" id="paymentAccordion">
                                    <details class="payment_option" data-method="card" open>
                                        <summary class="payment_option_summary">
                                            <span class="payment_option_label">
                                                <input class="payment_option_radio" type="radio" name="payment_method" value="card" checked>
                                                <span>Credit / Debit card</span>
                                            </span>
                                            <i class="ri-arrow-down-s-line payment_option_icon" aria-hidden="true"></i>
                                        </summary>
                                        <div class="payment_option_body">
                                            <div class="payment_option_hint">Pay securely with Visa, Mastercard, or American Express.</div>

                                            <div class="payment_fields" data-method-fields="card">
                                                <div class="payment_field">
                                                    <label class="payment_label" for="cardNumber">Card number</label>
                                                    <input class="payment_input" id="cardNumber" name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456" data-required="1">
                                                </div>

                                                <div class="payment_field">
                                                    <label class="payment_label" for="cardName">Name on card</label>
                                                    <input class="payment_input" id="cardName" name="card_name" autocomplete="cc-name" placeholder="J. Doe" data-required="1">
                                                </div>

                                                <div class="payment_field">
                                                    <label class="payment_label" for="cardExpiry">Expiry</label>
                                                    <input class="payment_input" id="cardExpiry" name="card_expiry" inputmode="numeric" autocomplete="cc-exp" placeholder="MM/YY" data-required="1">
                                                </div>

                                                <div class="payment_field">
                                                    <label class="payment_label" for="cardCvc">CVC</label>
                                                    <input class="payment_input" id="cardCvc" name="card_cvc" inputmode="numeric" autocomplete="cc-csc" placeholder="123" data-required="1">
                                                </div>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="payment_option" data-method="paypal">
                                        <summary class="payment_option_summary">
                                            <span class="payment_option_label">
                                                <input class="payment_option_radio" type="radio" name="payment_method" value="paypal">
                                                <span>PayPal</span>
                                            </span>
                                            <i class="ri-arrow-down-s-line payment_option_icon" aria-hidden="true"></i>
                                        </summary>
                                        <div class="payment_option_body">
                                            <div class="payment_option_hint">Pay with your PayPal account. Fast checkout and buyer protection.</div>
                                        </div>
                                    </details>

                                    <details class="payment_option" data-method="ideal">
                                        <summary class="payment_option_summary">
                                            <span class="payment_option_label">
                                                <input class="payment_option_radio" type="radio" name="payment_method" value="ideal">
                                                <span>iDEAL</span>
                                            </span>
                                            <i class="ri-arrow-down-s-line payment_option_icon" aria-hidden="true"></i>
                                        </summary>
                                        <div class="payment_option_body">
                                            <div class="payment_option_hint">Pay directly with your bank in the Netherlands using iDEAL.</div>

                                            <div class="payment_fields" data-method-fields="ideal">
                                                <div class="payment_field payment_field--full">
                                                    <label class="payment_label" for="idealBank">Select your bank</label>
                                                    <select class="payment_select" id="idealBank" name="ideal_bank" data-required="1">
                                                        <option value="" selected disabled>Choose bank</option>
                                                        <option value="abn_amro">ABN AMRO</option>
                                                        <option value="asn">ASN Bank</option>
                                                        <option value="bunq">bunq</option>
                                                        <option value="ing">ING</option>
                                                        <option value="knab">Knab</option>
                                                        <option value="rabobank">Rabobank</option>
                                                        <option value="regiobank">RegioBank</option>
                                                        <option value="sns">SNS</option>
                                                        <option value="triodos">Triodos Bank</option>
                                                        <option value="van_lanschot">Van Lanschot</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="payment_option" data-method="klarna">
                                        <summary class="payment_option_summary">
                                            <span class="payment_option_label">
                                                <input class="payment_option_radio" type="radio" name="payment_method" value="klarna">
                                                <span>Klarna</span>
                                            </span>
                                            <i class="ri-arrow-down-s-line payment_option_icon" aria-hidden="true"></i>
                                        </summary>
                                        <div class="payment_option_body">
                                            <div class="payment_option_hint">Pay later or split payments with Klarna (availability depends on region).</div>

                                            <div class="payment_fields" data-method-fields="klarna">
                                                <div class="payment_field">
                                                    <label class="payment_label" for="klarnaFirstName">First name</label>
                                                    <input class="payment_input" id="klarnaFirstName" name="klarna_first_name" autocomplete="given-name" placeholder="Noah" data-required="1">
                                                </div>

                                                <div class="payment_field">
                                                    <label class="payment_label" for="klarnaLastName">Last name</label>
                                                    <input class="payment_input" id="klarnaLastName" name="klarna_last_name" autocomplete="family-name" placeholder="Stortenbeker" data-required="1">
                                                </div>

                                                <div class="payment_field">
                                                    <label class="payment_label" for="klarnaEmail">Email</label>
                                                    <input class="payment_input" id="klarnaEmail" name="klarna_email" type="email" autocomplete="email" placeholder="name@email.com" data-required="1">
                                                </div>

                                                <div class="payment_field">
                                                    <label class="payment_label" for="klarnaPhone">Phone</label>
                                                    <input class="payment_input" id="klarnaPhone" name="klarna_phone" type="tel" autocomplete="tel" placeholder="+31 6 12345678" data-required="1">
                                                </div>

                                                <div class="payment_field payment_field--full">
                                                    <label class="payment_label" for="klarnaDob">Date of birth</label>
                                                    <input class="payment_input" id="klarnaDob" name="klarna_dob" placeholder="DD/MM/YYYY" data-required="1">
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>

                        <aside class="checkout_right">
                            <div class="checkout_card">
                                <div class="checkout_card_title">Order #{{ $order->id }}</div>

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

                                <div class="product_actions payment_actions">
                                    <a class="vacancy_create_btn" href="{{ route('checkout.show') }}" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">BACK</a>
                                    <button type="submit" class="vacancy_create_btn">PAY NOW</button>
                                </div>
                            </div>
                        </aside>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const accordion = document.getElementById('paymentAccordion');
            if (!accordion) return;

            function applyMethod(method) {
                accordion.querySelectorAll('[data-method-fields]').forEach(function (group) {
                    const groupMethod = group.getAttribute('data-method-fields');
                    const enabled = groupMethod === method;

                    group.querySelectorAll('input, select, textarea').forEach(function (el) {
                        if (enabled) {
                            el.disabled = false;
                            if (el.getAttribute('data-required') === '1') {
                                el.required = true;
                            }
                        } else {
                            el.required = false;
                            el.disabled = true;
                        }
                    });
                });
            }

            function openMethod(method) {
                accordion.querySelectorAll('details.payment_option').forEach(function (d) {
                    d.open = d.getAttribute('data-method') === method;
                });
            }

            accordion.addEventListener('toggle', function (e) {
                const details = e.target;
                if (!(details instanceof HTMLDetailsElement)) return;
                if (!details.open) return;

                accordion.querySelectorAll('details.payment_option[open]').forEach(function (d) {
                    if (d !== details) d.open = false;
                });

                const radio = details.querySelector('input[type="radio"][name="payment_method"]');
                if (radio) {
                    radio.checked = true;
                    applyMethod(radio.value);
                }
            }, true);

            accordion.querySelectorAll('input[type="radio"][name="payment_method"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    openMethod(radio.value);
                    applyMethod(radio.value);
                });
            });

            const openDetails = accordion.querySelector('details.payment_option[open]');
            if (openDetails) {
                const radio = openDetails.querySelector('input[type="radio"][name="payment_method"]');
                if (radio) {
                    radio.checked = true;
                    applyMethod(radio.value);
                }
            }
        })();
    </script>
@endsection
