@extends('layouts.app')

@section('title', 'Collections - FootLab')

@section('content')
    <section class="collection_section">
        <div class="collection_section_wrapper">
            <div class="collection_containers">
                <a href="{{ route('products.men') }}">
                    <div class="collection_tile spring_collection">
                        <img src="{{ asset('assets/spring.png') }}" alt="Men" />
                        <span class="collection_label">MEN</span>
                    </div>
                </a>
                <a href="{{ route('products.women') }}">
                    <div class="collection_tile summer_collection">
                        <img src="{{ asset('assets/summer.png') }}" alt="Women" />
                        <span class="collection_label">WOMEN</span>
                    </div>
                </a>
                <a href="{{ route('products.kids') }}">
                    <div class="collection_tile fall_collection">
                        <img src="{{ asset('assets/fall.png') }}" alt="Kids" />
                        <span class="collection_label">KIDS</span>
                    </div>
                </a>
                <a href="{{ route('products.index', ['discounted' => 1]) }}">
                    <div class="collection_tile winter_collection">
                        <img src="{{ asset('assets/winter.png') }}" alt="Sale" />
                        <span class="collection_label">SALE</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection
