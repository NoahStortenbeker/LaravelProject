<footer class="site_footer">
    <div class="site_footer_wrapper">
        <div class="footer_top">
            <div class="footer_brand">
                <img src="{{ asset('assets/Logo_wit.svg') }}" alt="FootLab Logo" class="footer_logo">
            </div>
            <div class="footer_columns">
                <div class="footer_column">
                    <h4 class="footer_heading">FOOTLAB</h4>
                    <ul class="footer_links">
                        <li><a href="#" class="footer_link">About Footlab <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">Updates <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('vacancies.index') }}" class="footer_link">Vacancy’s <i class="ri-arrow-right-up-line"></i></a></li>
                    </ul>
                </div>
                <div class="footer_column">
                    <h4 class="footer_heading">SHOP</h4>
                    <ul class="footer_links">
                        <li><a href="{{ route('products.men') }}" class="footer_link">Men <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('products.women') }}" class="footer_link">Women <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('products.kids') }}" class="footer_link">Kids <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('products.index', ['category' => 'Sneakers']) }}" class="footer_link">Sneakers <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('products.index', ['category' => 'Accessories']) }}" class="footer_link">Accessories <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('products.index', ['discounted' => 1]) }}" class="footer_link">Sale <i class="ri-arrow-right-up-line"></i></a></li>
                    </ul>
                </div>
                <div class="footer_column">
                    <h4 class="footer_heading">CUSTOMER SERVICE</h4>
                    <ul class="footer_links">
                        <li><a href="#" class="footer_link">Contact <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">FAQ <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">Shipping &amp; Delivery <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="{{ route('returns.index') }}" class="footer_link">Returns <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">Payment Methods <i class="ri-arrow-right-up-line"></i></a></li>
                    </ul>
                </div>
                <div class="footer_column">
                    <h4 class="footer_heading">FOLLOW US</h4>
                    <ul class="footer_links">
                        <li><a href="#" class="footer_link">Instagram <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">TikTok <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">Facebook <i class="ri-arrow-right-up-line"></i></a></li>
                        <li><a href="#" class="footer_link">Newsletter <i class="ri-arrow-right-up-line"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <span class="footer_copy">© {{ date('Y') }} FOOTLAB.DEV</span>
            <ul class="footer_legal">
                <li><a href="#" class="footer_link">DISCLAIMER</a></li>
                <li><a href="#" class="footer_link">COOKIES</a></li>
                <li><a href="#" class="footer_link">PRIVACYVERKLARING</a></li>
            </ul>
            <span class="footer_author">NOAH STORTENBEKER</span>
        </div>
    </div>
</footer>
