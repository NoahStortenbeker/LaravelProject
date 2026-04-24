@extends('layouts.app')

@section('title', 'Products - FootLab')

@section('content')
    <section class="products_page">
        <div class="products_wrapper">
            <div class="products_breadcrumbs">
                <a href="{{ route('products.index') }}" class="bc_link">PRODUCTS</a>
                @if($groupLabel)
                    <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                    <a href="{{ route('products.' . $group) }}" class="bc_link">{{ strtoupper($groupLabel) }}</a>
                @endif
                @if(($subcategory ?? '') !== '')
                    <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                    <span class="bc_current">{{ strtoupper($subcategory) }}</span>
                @elseif($category)
                    <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                    <span class="bc_current">{{ strtoupper($category) }}</span>
                @endif
            </div>

            <div class="products_layout">
                <aside class="products_filters">
                    <form id="productFiltersForm" method="GET" action="{{ url()->current() }}">
                        <input type="hidden" name="category" id="productsCategoryInput" value="{{ $category }}">
                        <input type="hidden" name="subcategory" id="productsSubcategoryInput" value="{{ $subcategory }}">

                        <details class="filter_accordion">
                            <summary class="filter_summary">
                                <span>Main category</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                <div class="filter_links" id="productsGroupLinks"></div>
                            </div>
                        </details>

                        <details class="filter_accordion">
                            <summary class="filter_summary">
                                <span>Category</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                <div class="filter_links" id="productsCategoryLinks"></div>
                            </div>
                        </details>

                        <details class="filter_accordion">
                            <summary class="filter_summary">
                                <span>Sub category</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                <div class="filter_links" id="productsSubcategoryLinks"></div>
                            </div>
                        </details>

                        <details class="filter_accordion">
                            <summary class="filter_summary">
                                <span>Product Size</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                @forelse($availableSizes as $size)
                                    <label class="filter_option">
                                        <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, $selectedSizes, true) ? 'checked' : '' }}>
                                        <span>{{ $size }}</span>
                                    </label>
                                @empty
                                    <div class="filter_empty">No sizes</div>
                                @endforelse
                            </div>
                        </details>

                        <details class="filter_accordion">
                            <summary class="filter_summary">
                                <span>Discount</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                <label class="filter_option">
                                    <input type="checkbox" name="discounted" value="1" {{ $discountedOnly ? 'checked' : '' }}>
                                    <span>Only discounted</span>
                                </label>
                                @foreach([10, 20, 30, 40, 50, 70] as $d)
                                    <label class="filter_option">
                                        <input type="checkbox" name="discounts[]" value="{{ $d }}" {{ in_array($d, $selectedDiscounts, true) ? 'checked' : '' }}>
                                        <span>{{ $d }}% Discount</span>
                                    </label>
                                @endforeach
                            </div>
                        </details>

                        <details class="filter_accordion">
                            <summary class="filter_summary">
                                <span>Price</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                <label class="filter_option">
                                    <input type="radio" name="sort" value="price_asc" {{ $sort === 'price_asc' ? 'checked' : '' }}>
                                    <span>Low → High</span>
                                </label>
                                <label class="filter_option">
                                    <input type="radio" name="sort" value="price_desc" {{ $sort === 'price_desc' ? 'checked' : '' }}>
                                    <span>High → Low</span>
                                </label>
                                <label class="filter_option">
                                    <input type="radio" name="sort" value="new" {{ $sort === 'new' ? 'checked' : '' }}>
                                    <span>Newest</span>
                                </label>
                                <div class="filter_price_range">
                                    <input class="filter_input" type="number" name="min_price" placeholder="Min" value="{{ is_numeric($minPrice) ? $minPrice : '' }}" min="0" step="0.01">
                                    <span class="range_sep">–</span>
                                    <input class="filter_input" type="number" name="max_price" placeholder="Max" value="{{ is_numeric($maxPrice) ? $maxPrice : '' }}" min="0" step="0.01">
                                </div>
                            </div>
                        </details>

                        <div class="filter_actions">
                            <a class="filter_clear" href="{{ url()->current() }}">Clear</a>
                            <button class="filter_apply" type="submit">Apply</button>
                        </div>
                    </form>
                </aside>

                <section class="products_results">
                    <div class="products_header_row">
                        <h2 class="products_title">PRODUCTS</h2>
                        <span class="products_count">{{ $products->total() }} items</span>
                    </div>

                    <div class="products_grid">
                        @forelse($products as $product)
                            @php
                                $disc = (int)($product->discount_percent ?? 0);
                                $base = (float)($product->price ?? 0);
                                $final = $disc > 0 ? max(0, $base * (1 - $disc / 100)) : $base;
                                $imgSrc = $product->image ?? asset('assets/placeholder.png');
                                $cat = $product->category ?? '';
                            @endphp
                            <a
                                class="new_product_card"
                                href="{{ route('products.show', $product->id) }}"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-image="{{ $imgSrc }}"
                                data-price="{{ $final }}"
                                data-category="{{ $cat }}"
                            >
                                <div class="new_product_media">
                                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" />
                                    <div class="new_product_overlay">
                                        <div class="overlay_actions">
                                            <i class="ri-shopping-basket-line"></i>
                                            <i class="ri-heart-3-line"></i>
                                        </div>
                                        @if(!(bool)($product->is_online ?? true))
                                            <span class="unavailable_text">Product unavailable</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="new_product_info">
                                    <h3>{{ $product->name }}</h3>
                                    @if($disc > 0)
                                        <span class="discount_badge">Disc {{ $disc }}%</span>
                                    @endif
                                    @if(($product->discount_percent ?? 0) > 0)
                                        <p class="new_product_price">
                                            <span class="new_product_price_original">€ {{ number_format($base, 0) }}</span>
                                            <span class="new_product_price_discounted">€ {{ number_format($final, 0) }}</span>
                                        </p>
                                    @else
                                        <p class="new_product_price">€ {{ number_format($base, 0) }}</p>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="products_empty">No products found</div>
                        @endforelse
                    </div>

                    @if($products->lastPage() > 1)
                        <div class="products_pagination">
                            @for($i = 1; $i <= $products->lastPage(); $i++)
                                <a class="page_link {{ $i === $products->currentPage() ? 'active' : '' }}" href="{{ $products->appends(request()->query())->url($i) }}">{{ $i }}</a>
                            @endfor
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </section>
@endsection
