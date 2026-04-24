@extends('layouts.guest')

@section('content')
@include('components.header')
<div class="admin_wrapper">
    <div class="admin_sidebar">
        <div class="admin_profile">
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
            <li class="nav_item" data-target="panel-users"><span>Users</span></li>
            <li class="nav_item" data-target="panel-orders"><span>Orders</span></li>
            <li class="nav_item" data-target="panel-vacancies"><span>Vacancy’s</span></li>
            <li class="nav_item" data-target="panel-product"><span>Product</span></li>
            <li class="nav_item" data-target="panel-Product-Overview"><span>Product Overview</span></li>
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

    <section class="admin_content">
        @if(session('admin_order_updated'))
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
        <div id="panel-users" class="panel active">
            <div class="admin_list_header">
                <span class="admin_list_title">Users</span>
                <div class="admin_search_btn" id="adminSearchBtn">
                    <input type="text" id="adminSearchInput" class="admin_search_input" placeholder="Search username" aria-label="Search username">
                    <i class="ri-search-line"></i>
                </div>
            </div>
            @php
                $users = \App\Models\User::select('id','name','username','email','phone_number','is_admin','profile_photo_path','last_seen_at')->orderBy('username')->get();
            @endphp
            <div class="admin_users_list">
                @foreach($users as $u)
                    @php
                        $isAdmin = $u->is_admin ?? false;
                        $online = (Auth::id() === $u->id) || ($u->last_seen_at && $u->last_seen_at->gt(now()->subMinutes(2)));
                        $status = $online ? 'online' : 'offline';
                        $photoUrl = $u->profile_photo_url ?? null;
                        $initial = strtoupper(substr(($u->username ?? $u->name ?? ''), 0, 1));
                    @endphp
                    <div class="user_row" data-username="{{ strtolower($u->username ?? '') }}" data-role="{{ $isAdmin ? 'admin' : 'user' }}" data-status="{{ $status }}">
                        <div class="user_cell user_avatar">
                            <span class="avatar_60">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $u->username ?? $u->name }}">
                                @else
                                    <span class="avatar_initial">{{ $initial }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="user_cell">{{ $u->name }}</div>
                        <div class="user_cell user_username">{{ $u->username }}</div>
                        <div class="user_cell">{{ $u->email }}</div>
                        <div class="user_cell">{{ $u->phone_number ?? '' }}</div>
                        <div class="user_cell"><span class="admin_badge">{{ $isAdmin ? 'Admin' : 'User' }}</span></div>
                        <div class="user_cell"><span class="admin_status {{ $status }}">{{ ucfirst($status) }}</span></div>
                    </div>
                @endforeach
            </div>
            <div class="admin_pagination">
                <div class="pg_numbers" id="adminPages"></div>
            </div>
        </div>
        <div id="panel-orders" class="panel">
            <h2 class="admin_list_title">Orders</h2>
            @php
                $orders = \App\Models\Order::query()
                    ->with('user')
                    ->orderByDesc('created_at')
                    ->limit(50)
                    ->get();
            @endphp

            <div class="orders_card admin_orders">
                <div class="orders_header">
                    <div class="orders_hcell orders_hcell--id">#</div>
                    <div class="orders_hcell orders_hcell--user">user</div>
                    <div class="orders_hcell orders_hcell--date">order date</div>
                    <div class="orders_hcell orders_hcell--amount">amount</div>
                    <div class="orders_hcell orders_hcell--status">status</div>
                </div>

                <div class="orders_body">
                    @forelse($orders as $order)
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
                            $userLabel = $order->user?->username ?: ($order->user?->name ?: 'User #'.$order->user_id);
                        @endphp

                        <div class="orders_row">
                            <div class="orders_cell orders_cell--id">{{ $order->id }}</div>
                            <div class="orders_cell orders_cell--user">{{ $userLabel }}</div>
                            <div class="orders_cell orders_cell--date">{{ optional($order->created_at)->format('d / m / Y') }}</div>
                            <div class="orders_cell orders_cell--amount">{{ $symbol }} {{ $amount }}</div>
                            <div class="orders_cell orders_cell--status">
                                <span class="status_badge status_badge--{{ $statusKey }}">{{ $statusLabel }}</span>
                                <form method="POST" action="{{ route('admin.orders.update', ['order' => $order->id]) }}" class="admin_order_status_form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="order_status_select" aria-label="Order status">
                                        <option value="pending_payment" @selected($rawStatus === 'pending_payment' || $rawStatus === 'pending')>Pending</option>
                                        <option value="processing" @selected($rawStatus === 'processing' || $rawStatus === 'paid')>Processing</option>
                                        <option value="completed" @selected($rawStatus === 'completed')>Completed</option>
                                        <option value="canceling" @selected($rawStatus === 'canceling')>Canceling</option>
                                        <option value="canceled" @selected($rawStatus === 'canceled' || $rawStatus === 'cancelled')>Canceled</option>
                                    </select>
                                    <button type="submit" class="order_status_save_btn">Save</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="orders_empty">No orders yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div id="panel-vacancies" class="panel">
            <h2 class="admin_list_title">Create Vacancy</h2>
            <form class="vacancy_form" id="vacancyForm">
                @csrf
                <input type="file" id="vacancyImages" name="images[]" accept="image/*" multiple style="display:none;" data-max="3">
                <div class="vacancy_top_row">
                    <button type="button" class="vacancy_image_btn" id="vacancyImageBtn">Select image</button>
                    <div class="vacancy_preview" id="vacancyPreview"></div>
                </div>
                <div class="vacancy_grid vacancy_grid--two">
                    <div class="form_group">
                        <label>Title</label>
                        <input type="text" id="vacancyTitle" name="title">
                    </div>
                    <div class="form_group">
                        <label>Subtitle</label>
                        <input type="text" id="vacancySubtitle" name="subtitle">
                    </div>
                </div>
                <div class="vacancy_grid vacancy_grid--three">
                    <div class="form_group">
                        <label>Hours</label>
                        <input type="text" id="vacancyHours" name="hours">
                    </div>
                    <div class="form_group">
                        <label>Location</label>
                        <input type="text" id="vacancyLocation" name="location">
                    </div>
                    <div class="form_group">
                        <label>Employment type</label>
                        <input type="text" id="vacancyEmployment" name="employment_type">
                    </div>
                </div>
                <div class="vacancy_grid vacancy_grid--desc">
                    <div class="form_group">
                        <label>Description</label>
                        <textarea id="vacancyDescription" name="description"></textarea>
                    </div>
                </div>
                <div class="vacancy_actions">
                    <button type="button" class="vacancy_create_btn" id="vacancyCreateBtn">CREATE</button>
                </div>
            </form>
        </div>
        <div id="panel-product" class="panel">
            <h2 class="admin_list_title">Add Products</h2>
            <form class="product_form" id="productForm">
                @csrf
                <input type="file" id="productImages" name="images[]" accept="image/*" multiple style="display:none;" data-max="6">
                <div class="product_top_row">
                    <div class="product_top_left">
                        <button type="button" class="vacancy_image_btn" id="productImageBtn">Select image</button>
                        <div class="vacancy_preview" id="productPreview"></div>
                    </div>
                    <button type="button" class="sizes_btn" id="sizesBtn">SIZES</button>
                </div>
                <div class="product_grid product_grid--two">
                    <div class="form_group">
                        <label>Category</label>
                        <div class="dropdown" id="productCategoryDropdown">
                            <button type="button" class="dropdown_toggle"><span class="dropdown_label">Select a category</span><i class="ri-arrow-down-s-line"></i></button>
                            <div class="dropdown_menu_products" id="productCategoryMenu" aria-hidden="true"></div>
                            <input type="hidden" id="productCategory" name="category">
                            <input type="hidden" id="productCategoryGroup" name="taxonomy_group">
                            <input type="hidden" id="productCategoryKey" name="taxonomy_category">
                            <input type="hidden" id="productSubcategoryKey" name="taxonomy_subcategory">
                        </div>
                    </div>
                    <div class="form_group">
                        <label>SKU number</label>
                        <input type="number" id="productSku" name="sku">
                    </div>
                </div>
                <div class="product_grid product_grid--two">
                    <div class="form_group">
                        <label>Product name</label>
                        <input type="text" id="productName" name="name">
                    </div>
                    <div class="form_group">
                        <label>Price</label>
                        <div class="currency_input">
                            <input type="number" step="1" id="productPrice" name="price" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="product_grid product_grid--desc">
                    <div class="form_group">
                        <label>Description</label>
                        <textarea id="productDescription" name="description"></textarea>
                    </div>
                </div>
                <div class="product_actions">
                    <span></span>
                    <button type="button" class="vacancy_create_btn" id="productCreateBtn">CREATE</button>
                </div>
            </form>
            <div class="sizes_overlay" id="sizesOverlay" aria-hidden="true">
                <div class="sizes_box">
                    <h3 class="sizes_title">Select sizes and amounts</h3>
                    <div class="sizes_groups" id="sizesGroups"></div>
                    <div class="sizes_footer">
                        <button type="button" class="sizes_cancel">Cancel</button>
                        <button type="button" class="sizes_save" id="sizesSaveBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="panel-Product-Overview" class="panel">
            <h2 class="admin_list_title">Product Overview</h2>
            <div class="product_overview_list" id="productOverviewList"></div>
            <div class="product_overview_footer" id="productOverviewFooter">
                <div class="product_pagination" id="productPagination"></div>
                <div>
                    <button type="button" class="po_delete_btn" id="productOverviewDeleteBtn">DELETE</button>
                    <button type="button" class="po_save_btn" id="productOverviewSaveBtn">SAVE</button>
                </div>
            </div>
        </div>
    </section>
</div>
@vite('resources/js/components/account.js')
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
@endsection
