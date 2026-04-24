@extends('layouts.guest')

@section('content')
@include('components.header')
<div class="account_wrapper">
    <div class="account_sidebar">
        <div class="account_profile">
            @php $photo = Auth::user()->profile_photo_url ?? null; @endphp
            <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;" data-photo-url="{{ route('users.photo') }}">
            <span class="avatar_60" id="avatarClickable">
                @if($photo)
                    <img src="{{ $photo }}" alt="{{ Auth::user()->username ?? Auth::user()->name }}">
                @else
                    <i class="ri-user-3-fill"></i>
                @endif
                <span class="avatar_overlay">change profile</span>
            </span>
            <span class="account_username">{{ Auth::user()->username ?? Auth::user()->name }}</span>
        </div>
        <ul class="account_nav" id="accountNav">
            <li class="nav_item" data-target="panel-account"><span>Account</span></li>
            <li class="nav_item" data-target="panel-wishlist"><span>Wishlist</span></li>
            <li class="nav_item" data-target="panel-address"><span>Address</span></li>
            <li class="nav_item" data-target="panel-orders"><span>Order history</span></li>
            <span class="nav_indicator"></span>
        </ul>
        <div class="logout_container">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout_btn">
                    <span class="logout_btn_text">Log out</span>
                    <i class="ri-logout-box-r-line logout_btn_icon" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>

    <section class="account_content">
        @if (session('status'))
            <div id="statusUpdatedFlag" style="display:none;"></div>
        @endif
        <div class="toast_top" id="statusToast" aria-hidden="true">
            <span class="toast_text">changes successful made</span>
            <i class="toast_icon ri-check-line" aria-hidden="true"></i>
            <button class="toast_close" aria-label="Close"><i class="ri-close-line"></i></button>
        </div>
        <div class="crop_overlay" id="cropOverlay" aria-hidden="true">
            <div class="crop_box">
                <div class="crop_viewport">
                    <img id="cropImage" alt="Crop preview">
                    <div class="crop_mask"></div>
                </div>
                <div class="crop_controls">
                    <input type="range" id="cropZoom" min="1" max="3" step="0.01" value="1">
                    <div class="crop_actions">
                        <button type="button" class="crop_cancel">Cancel</button>
                        <button type="button" class="crop_save">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="panel-account" class="panel active">
            <form method="POST" action="{{ route('users.update') }}" class="account_form">
                @csrf
                @method('patch')
            <div class="form_row">
                <div class="form_group">
                    <label>Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}">
                    @error('name')
                        <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form_group">
                    <label>User Name</label>
                    <input type="text" id="username" name="username" value="{{ old('username', Auth::user()->username ?? '') }}" placeholder="User name">
                    @error('username')
                        <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form_group form_group--full">
                <label>E-mail address</label>
                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" placeholder="Email address">
                @error('email')
                    <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                @enderror
            </div>
            <div class="form_row">
                <div class="form_group">
                    <label>Phone number</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number ?? '') }}" placeholder="+31">
                    @error('phone_number')
                        <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form_group">
                    <label>Date of Birth</label>
                    <div class="select_input_wrapper" id="dobWrapper">
                        <input type="text" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional(Auth::user()->date_of_birth)->format('Y-m-d')) }}" placeholder="Select date" readonly>
                        <i class="ri-arrow-down-s-line dropdown-icon" id="dobToggle"></i>
                        <div class="datepicker_dropdown" id="dobPicker" aria-hidden="true">
                            <div class="datepicker_header">
                                <i class="ri-arrow-left-s-line prev"></i>
                                <span class="month_label">Month YYYY</span>
                                <i class="ri-arrow-right-s-line next"></i>
                            </div>
                            <div class="year_dropdown" aria-hidden="true">
                                <div class="year_header">
                                    <i class="ri-arrow-left-s-line year-prev"></i>
                                    <span class="year_range">0000–0000</span>
                                    <i class="ri-arrow-right-s-line year-next"></i>
                                </div>
                                <div class="year_grid"></div>
                            </div>
                            <div class="weekday_row">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="datepicker_grid"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="form_group">
                    <label>Password</label>
                    <div class="password_input_wrapper">
                        <input type="password">
                        <i class="ri-eye-off-line toggle-password"></i>
                    </div>
                </div>
                <div class="form_group">
                    <label>Confirm password</label>
                    <div class="password_input_wrapper">
                        <input type="password">
                        <i class="ri-eye-off-line toggle-password"></i>
                    </div>
                </div>
            </div>
            <div class="form_actions">
                <button class="confirm_btn">
                    <span class="btn_home_label">confirm changes</span>
                    <div class="btn_home_mask">
                        <span class="btn_home_label">confirm changes</span>
                    </div>
                </button>
            </div>
            </form>
        </div>

        <div id="panel-wishlist" class="panel"></div>
        <div id="panel-address" class="panel">
            <form method="POST" action="{{ route('users.address.update') }}" class="account_form" id="addressForm">
                @csrf
                @method('patch')
                <div class="form_group form_group--full">
                    <label>Full Address</label>
                    <input type="text" id="address_line" name="address_line" value="{{ old('address_line', Auth::user()->address_line ?? '') }}" placeholder="Street, number, unit">
                    @error('address_line')
                        <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form_row">
                    <div class="form_group">
                        <label>zip-code</label>
                        <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', Auth::user()->zip_code ?? '') }}" placeholder="1234 AB">
                        @error('zip_code')
                            <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form_group">
                        <label>City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', Auth::user()->city ?? '') }}" placeholder="Amsterdam">
                        @error('city')
                            <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form_row">
                    <div class="form_group">
                        <label>Country</label>
                        @php $currentCountry = old('country', Auth::user()->country ?? ''); @endphp
                        <div id="countryDropdown" class="dropdown" data-name="country">
                            <button type="button" class="dropdown_toggle">
                                <span class="dropdown_label">{{ $currentCountry ?: 'Select a country' }}</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div id="countryMenu" class="dropdown_menu_products" aria-hidden="true"></div>
                            <input type="hidden" id="country" name="country" value="{{ $currentCountry }}">
                        </div>
                        @error('country')
                            <div style="color:#ff4d4f; margin-top:6px; font-size:14px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form_actions">
                    <button class="confirm_btn">
                        <span class="btn_home_label">Confirm changes</span>
                        <div class="btn_home_mask">
                            <span class="btn_home_label">Confirm changes</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
        <div id="panel-orders" class="panel">
            @php
                $ordersList = $orders ?? collect();
            @endphp

            @if (session('order_action'))
                <div class="toast_top open" style="position: static; transform: none; opacity: 1; pointer-events: auto; margin: 0 0 16px;">
                    <span class="toast_text">{{ session('order_action') }}</span>
                </div>
            @endif

            <div class="orders_card">
                <div class="orders_header">
                    <div class="orders_hcell orders_hcell--id">#</div>
                    <div class="orders_hcell orders_hcell--date">order date</div>
                    <div class="orders_hcell orders_hcell--amount">amount</div>
                    <div class="orders_hcell orders_hcell--status">status</div>
                </div>

                <div class="orders_body">
                    @forelse($ordersList as $order)
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
                            $currency = strtoupper((string) ($order->currency ?? 'EUR'));
                            $symbol = $currency === 'EUR' ? '€' : $currency;
                            $amount = number_format((float) ($order->total ?? 0), 2, '.', '');
                            $canCancel = in_array($statusKey, ['pending', 'processing'], true) && $statusKey !== 'canceling';
                        @endphp

                        <div class="orders_row">
                            <div class="orders_cell orders_cell--id">{{ $order->id }}</div>
                            <div class="orders_cell orders_cell--date">{{ optional($order->created_at)->format('d / m / Y') }}</div>
                            <div class="orders_cell orders_cell--amount">{{ $symbol }} {{ $amount }}</div>
                            <div class="orders_cell orders_cell--status">
                                <span class="status_badge status_badge--{{ $statusKey }}">{{ $statusLabel }}</span>
                                @if($canCancel)
                                    <button
                                        type="button"
                                        class="order_cancel_btn"
                                        data-cancel-action="{{ route('users.orders.cancel', ['order' => $order->id]) }}"
                                        data-order-id="{{ $order->id }}"
                                    >Cancel</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="orders_empty">No orders yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <div class="modal_overlay" id="cancelOrderModal" aria-hidden="true">
        <div class="modal_box" role="dialog" aria-modal="true" aria-labelledby="cancelOrderTitle">
            <div class="modal_title" id="cancelOrderTitle">Cancel order</div>
            <div class="modal_text" id="cancelOrderText">Are you sure you want to cancel this order?</div>

            <form method="POST" id="cancelOrderForm" action="">
                @csrf
                <div class="modal_actions">
                    <button type="button" class="modal_btn modal_btn--ghost" id="cancelOrderClose">Keep order</button>
                    <button type="submit" class="modal_btn modal_btn--danger" id="cancelOrderConfirm">Cancel order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@vite('resources/js/components/account.js')
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
@endsection
