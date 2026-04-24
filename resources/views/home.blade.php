@extends('layouts.app')

@section('title', 'FootLab')

@section('content')
    <section class="hero_section">
        <div class="hero_section_wrapper">
            <div class="hero_copy">
                <h1>YOU ARE SHOPPING <br>
                    FOR GREATNESS
                </h1>
                <a href="{{ route('products.index') }}"><button class="btn_home">   
                    <span class="btn_home_label">Shop now</span>
                    <div class="btn_home_mask">
                        <span class="btn_home_label">Shop now</span>
                    </div>
                </button></a>
            </div>
            <div class="hero_image">
                <img src="{{ asset('assets/PNG image-home.png') }}" alt="FootLab Hero" />
            </div>
        </div>
    </section>

    <section class="collection_section">
        <div class="collection_section_wrapper">
            <div class="collection_containers">
                <a href=""><div class="collection_tile spring_collection">
                    <img src="{{ asset('assets/spring.png') }}" alt="Spring" />
                    <span class="collection_label">SPRING</span>
                </div></a>
                <a href=""><div class="collection_tile summer_collection">
                    <img src="{{ asset('assets/summer.png') }}" alt="Summer" />
                    <span class="collection_label">SUMMER</span>
                </div></a>
                <a href=""><div class="collection_tile fall_collection">
                    <img src="{{ asset('assets/fall.png') }}" alt="Fall" />
                    <span class="collection_label">FALL</span>
                </div></a>
                <a href=""><div class="collection_tile winter_collection">
                    <img src="{{ asset('assets/winter.png') }}" alt="Winter" />
                    <span class="collection_label">WINTER</span>
                </div></a>
            </div>
        </div>
    </section>

    <section class="new_products_section">
        <div class="new_products_section_wrapper">
            <div class="new_products_title">
                <h2>NEW PRODUCTS</h2>
            </div>
            <div class="np_slider">
                <div class="new_products_grid">
                @foreach($products as $product)
                    @php
                        $disc = (int)($product->discount_percent ?? 0);
                        $base = (float)($product->price ?? 0);
                        $final = $disc > 0 ? max(0, $base * (1 - $disc / 100)) : $base;
                        $imgSrc = $product->image ?? asset('assets/placeholder.png');
                        $cat = $product->category ?? '';
                    @endphp
                    <div
                        class="new_product_card {{ ($product->is_online ?? true) ? '' : 'offline' }}"
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
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
