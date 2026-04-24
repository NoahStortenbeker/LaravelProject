@extends('layouts.app')

@section('title', ($product->name ?? 'Product') . ' - FootLab')

@section('content')
    @php
        $disc = (int)($product->discount_percent ?? 0);
        $base = (float)($product->price ?? 0);
        $final = $disc > 0 ? max(0, $base * (1 - $disc / 100)) : $base;
        $imgSrc = $product->image ?? asset('assets/placeholder.png');
        $category = (string)($product->category ?? '');
        $sizes = ($product->sizes ?? collect())->filter(fn($s) => (int)($s->amount ?? 0) > 0)->values();
    @endphp

    <section class="product_show_page">
        <div class="product_show_wrapper">
            <div class="products_breadcrumbs">
                <a href="{{ route('products.index') }}" class="bc_link">PRODUCTS</a>
                @if($category)
                    <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                    <span class="bc_current">{{ strtoupper($category) }}</span>
                @endif
            </div>

            <div class="product_show_layout">
                <div class="product_show_media">
                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}">
                </div>
                <div class="product_show_info">
                    <div class="product_show_title_row">
                        <h1 class="product_show_title">{{ $product->name }}</h1>
                        <div class="product_show_title_actions">
                            @if($disc > 0)
                                <span class="product_show_disc_badge">Disc {{ $disc }}%</span>
                            @endif
                            <button
                                class="product_show_wishlist"
                                type="button"
                                id="productWishlistToggle"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-image="{{ $imgSrc }}"
                                data-price="{{ $final }}"
                                data-category="{{ $category }}"
                                aria-label="Wishlist"
                            >
                                <i class="ri-heart-3-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="product_show_price_row">
                        @if($disc > 0)
                            <span class="product_show_price_original">€ {{ number_format($base, 0) }}</span>
                        @endif
                        <span class="product_show_price_final">€ {{ number_format($final, 0) }}</span>
                    </div>

                    @if($product->description)
                        <p class="product_show_desc">{{ $product->description }}</p>
                    @endif

                    <div class="product_show_actions">
                        <select class="product_show_size_select" id="productSizeSelect" {{ $sizes->count() ? '' : 'disabled' }}>
                            <option value="" selected>select size</option>
                            @foreach($sizes as $s)
                                <option value="{{ $s->size_label }}">{{ $s->size_label }}</option>
                            @endforeach
                        </select>
                        <button
                            class="product_show_add_btn"
                            type="button"
                            id="productAddToCartBtn"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-image="{{ $imgSrc }}"
                            data-price="{{ $final }}"
                        >
                            ADD TO CART
                        </button>
                    </div>

                    @if(($recentlyViewed ?? collect())->count())
                        <div class="recently_viewed">
                            <h2 class="recently_viewed_title">Recently viewed product</h2>
                            <div class="recently_viewed_grid">
                                @foreach($recentlyViewed as $rv)
                                    @php
                                        $rvDisc = (int)($rv->discount_percent ?? 0);
                                        $rvBase = (float)($rv->price ?? 0);
                                        $rvFinal = $rvDisc > 0 ? max(0, $rvBase * (1 - $rvDisc / 100)) : $rvBase;
                                        $rvImg = $rv->image ?? asset('assets/placeholder.png');
                                    @endphp
                                    <a class="recent_card" href="{{ route('products.show', $rv->id) }}">
                                        <div class="recent_media">
                                            <img src="{{ $rvImg }}" alt="{{ $rv->name }}">
                                            @if($rvDisc > 0)
                                                <span class="recent_disc_badge">Disc {{ $rvDisc }}%</span>
                                            @endif
                                        </div>
                                        <div class="recent_meta">
                                            <div class="recent_name">{{ $rv->name }}</div>
                                            <div class="recent_price_row">
                                                @if($rvDisc > 0)
                                                    <span class="recent_price_original">€ {{ number_format($rvBase, 0) }}</span>
                                                @endif
                                                <span class="recent_price_final">€ {{ number_format($rvFinal, 0) }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
