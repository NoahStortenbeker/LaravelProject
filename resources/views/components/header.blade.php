<link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">

<div class="top_bar">
    <header>
        <div class="logo_container">
            <a href="{{ route('home') }}"><img src="{{ asset('assets/Logo_wit.svg') }}" alt="FootLab Logo" class="logo_icon"></a>
        </div>

        <nav class="navbar">
            <ul>
                <li><a href="{{ route('home') }}" class="nav_item_link {{ request()->routeIs('home') ? 'active' : '' }}">HOME</a></li>
                <li>
                    <a href="#" class="nav_item_link {{ request()->routeIs('products.*') ? 'active' : '' }}" id="products_toggle">PRODUCTS <i class="ri-arrow-down-s-line"></i></a>
                    <div class="dropdown_menu dropdown_menu--products">
                        <div class="dropdown_top">
                            <a href="#" class="tab_link active" data-tab="men">MEN</a>
                            <a href="#" class="tab_link" data-tab="women">WOMEN</a>
                            <a href="#" class="tab_link" data-tab="kids">KIDS</a>
                        </div>
                        <div class="dropdown_slider_wrapper">
                            <div class="dropdown_panel panel_main">
                                <div class="tab_slider">
                                    <div class="category_list" id="list_men"></div>
                                    <div class="category_list" id="list_women"></div>
                                    <div class="category_list" id="list_kids"></div>
                                </div>
                            </div>
                            <div class="dropdown_panel panel_sub" id="sub_clothing">
                                <div class="breadcrumbs" id="breadcrumbs"></div>
                                <a href="{{ route('products.index') }}" class="sub_link">All Items</a>
                                <a href="{{ route('products.index', ['category' => 'Shirts']) }}" class="sub_link">Shirts</a>
                                <a href="{{ route('products.index', ['category' => 'T-Shirts']) }}" class="sub_link">T-Shirts</a>
                                <a href="{{ route('products.index', ['category' => 'Jeans']) }}" class="sub_link">Jeans</a>
                                <a href="{{ route('products.index', ['category' => 'Cargos']) }}" class="sub_link">Cargo’s</a>
                                <a href="{{ route('products.index', ['category' => 'Sweaters']) }}" class="sub_link">Sweaters</a>
                                <a href="{{ route('products.index', ['category' => 'Hoodies']) }}" class="sub_link">Hoodies</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="#" class="nav_item_link {{ request()->routeIs('collections.*') ? 'active' : '' }}" id="collections_toggle">COLLECTIONS <i class="ri-arrow-down-s-line"></i></a>
                    <div class="dropdown_menu dropdown_menu--collections">
                        <div class="dropdown_slider_wrapper">
                            <div class="dropdown_panel panel_main">
                                <div class="category_list">
                                    <a href="{{ route('collections.index') }}" class="cat_link">All Collections</a>
                                    <a href="{{ route('products.men') }}" class="cat_link">Men</a>
                                    <a href="{{ route('products.women') }}" class="cat_link">Women</a>
                                    <a href="{{ route('products.kids') }}" class="cat_link">Kids</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="header_actions">
            <div class="icon_btn"><i class="ri-search-2-line"></i></div>
            <div class="icon_btn"><a href="#" id="cartToggleBtn"><i class="ri-shopping-bag-line"></i></a></div>
        </div>
    </header>

    <div class="cart_backdrop" id="cartBackdrop" aria-hidden="true"></div>
    <aside class="cart_drawer" id="cartDrawer" aria-hidden="true">
        <div class="cart_header">
            <h3>SHOPPING CART</h3>
            <button class="cart_close" id="cartCloseBtn" aria-label="Close"><i class="ri-close-line"></i></button>
        </div>
        <div class="cart_body">
            <div class="cart_empty">
                <p>cart is empty!</p>
            </div>
            <hr class="cart_divider">
            <div class="cart_actions">
                <button class="cart_btn continue_btn" id="cartContinueBtn">CONTINUE SHOPPING</button>
                <button class="cart_btn checkout_btn" id="cartCheckoutBtn">CHECKOUT</button>
            </div>
        </div>
    </aside>

    <div class="account_dropdown_container">
        @php $onAuthPages = request()->routeIs('login') || request()->routeIs('register'); @endphp
        @if(Auth::check() && !$onAuthPages)
            <a href="#" class="contact_btn account_summary">
                <span class="avatar_60">
                    @php $photo = Auth::user()->profile_photo_url ?? null; @endphp
                    @if($photo)
                        <img src="{{ $photo }}" alt="{{ Auth::user()->name }}">
                    @else
                        <i class="ri-user-3-fill"></i>
                    @endif
                </span>
                <span class="account_username">{{ Auth::user()->username ?? Auth::user()->name }}</span>
            </a>
            <div class="account_dropdown_menu">
                @if(Auth::user()->is_admin ?? false)
                    <a href="{{ route('admin.dashboard', ['panel' => 'users']) }}" class="account_link"><span>Admin Panel</span></a>
                    <a href="{{ route('admin.dashboard', ['panel' => 'vacancies']) }}" class="account_link"><span>Vacancy’s</span></a>
                    <a href="{{ route('admin.dashboard', ['panel' => 'product']) }}" class="account_link"><span>Product</span></a>
                    <a href="{{ route('admin.dashboard', ['panel' => 'Product-Overview']) }}" class="account_link"><span>Product Overview</span></a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="account_link sign_out"><span>SIGN OUT</span></button>
                    </form>
                @else
                    <a href="{{ route('users.dashboard', ['panel' => 'profile']) }}" class="account_link"><span>My Account</span></a>
                    <a href="{{ route('users.dashboard', ['panel' => 'orders']) }}" class="account_link"><span>My Purchases</span></a>
                    <a href="{{ route('users.dashboard', ['panel' => 'returns']) }}" class="account_link"><span>Returns</span></a>
                    <a href="{{ route('users.dashboard', ['panel' => 'wishlist']) }}" class="account_link"><span>My Wishlist</span> <i class="ri-heart-line"></i></a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="account_link sign_out"><span>SIGN OUT</span></button>
                    </form>
                @endif
            </div>
        @else
            <a href="{{ route('login') }}" class="contact_btn">ACCOUNT</a>
            <div class="account_dropdown_menu">
                <a href="{{ route('login') }}" class="account_link transition-link"><span>Login</span> <i class="ri-arrow-right-line"></i></a>
                <a href="{{ route('register') }}" class="account_link transition-link"><span>Register</span> <i class="ri-arrow-right-line"></i></a>
            </div>
        @endif
    </div>
</div>
