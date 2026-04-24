<header>
    <div class="logo_container">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/Logo_wit.svg') }}" alt="FootLab Logo" class="logo_icon">
        </a>
    </div>

    <nav class="navbar">
        <ul>
            <li>
                <a href="{{ route('home') }}" class="nav_item_link {{ Request::is('/') ? 'active' : '' }}">
                    HOME
                </a>
            </li>
            <li>
                <a href="#" class="nav_item_link" id="products_toggle">
                    PRODUCTS <i class="ri-arrow-down-s-line"></i>
                </a>
                <div class="dropdown_menu">
                    <div class="dropdown_top">
                        <a href="#" class="tab_link active" data-tab="men">MEN</a>
                        <a href="#" class="tab_link" data-tab="women">WOMEN</a>
                        <a href="#" class="tab_link" data-tab="kids">KIDS</a>
                    </div>
                    <div class="dropdown_slider_wrapper">
                        <!-- Main Categories -->
                        <div class="dropdown_panel panel_main">
                            <div class="tab_slider">
                                <!-- Men Categories -->
                                <div class="category_list" id="list_men"></div>
                                <!-- Women Categories -->
                                <div class="category_list" id="list_women"></div>
                                <!-- Kids Categories -->
                                <div class="category_list" id="list_kids"></div>
                            </div>
                        </div>
                        <!-- Sub Categories -->
                        <div class="dropdown_panel panel_sub" id="sub_clothing">
                            <div class="breadcrumbs" id="breadcrumbs">
                                <!-- Dynamic Breadcrumbs -->
                            </div>
                            <a href="#" class="sub_link">All Items</a>
                            <a href="#" class="sub_link">Shirts</a>
                            <a href="#" class="sub_link">T-Shirts</a>
                            <a href="#" class="sub_link">Jeans</a>
                            <a href="#" class="sub_link">Cargo's</a>
                            <a href="#" class="sub_link">Sweaters</a>
                            <a href="#" class="sub_link">Hoodies</a>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <a href="#" class="nav_item_link">
                    COLLECTIONS<i class="ri-arrow-down-s-line"></i>
                </a>
            </li>
        </ul>
    </nav>

    <div class="header_actions">
        <div class="icon_btn">
            <i class="ri-shopping-bag-line"></i>
        </div>
        <div class="icon_btn">
            <i class="ri-search-2-line"></i>
        </div>
    </div>
</header>