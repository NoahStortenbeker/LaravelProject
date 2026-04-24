@extends('layouts.app')

@section('title', 'Collection - FootLab')

@section('content')
    @php
        $slug = strtolower(trim((string) ($slug ?? '')));
        $group = in_array($slug, ['men', 'women', 'kids'], true) ? $slug : null;
        $groupLabel = $group === 'men' ? 'Men' : ($group === 'women' ? 'Women' : ($group === 'kids' ? 'Kids' : null));

        $productsRoute = $group ? ('products.'.$group) : null;
        $allHref = $group ? route($productsRoute) : route('collections.index');

        $categories = collect();
        $subcategoriesByCategory = [];
        if ($group) {
            $categories = \App\Models\Product::query()
                ->where('is_online', true)
                ->where('taxonomy_group', $group)
                ->whereNotNull('taxonomy_category')
                ->select('taxonomy_category')
                ->distinct()
                ->orderBy('taxonomy_category')
                ->pluck('taxonomy_category')
                ->filter(fn ($v) => trim((string) $v) !== '')
                ->values();

            foreach ($categories as $cat) {
                $subs = \App\Models\Product::query()
                    ->where('is_online', true)
                    ->where('taxonomy_group', $group)
                    ->where('taxonomy_category', $cat)
                    ->whereNotNull('taxonomy_subcategory')
                    ->select('taxonomy_subcategory')
                    ->distinct()
                    ->orderBy('taxonomy_subcategory')
                    ->pluck('taxonomy_subcategory')
                    ->filter(fn ($v) => trim((string) $v) !== '')
                    ->values();

                $subcategoriesByCategory[$cat] = $subs;
            }
        }
    @endphp

    <section class="products_page">
        <div class="products_wrapper">
            <div class="products_breadcrumbs">
                <a href="{{ route('collections.index') }}" class="bc_link">COLLECTIONS</a>
                @if($groupLabel)
                    <span class="bc_sep"><i class="ri-arrow-right-s-line"></i></span>
                    <span class="bc_current">{{ strtoupper($groupLabel) }}</span>
                @endif
            </div>

            <h2 class="admin_list_title" style="margin-bottom: 24px;">{{ $groupLabel ? ($groupLabel.' collection') : 'Collection' }}</h2>

            @if(! $group)
                <div class="products_empty">Collection not found</div>
            @else
                <div class="products_layout">
                    <aside class="products_filters">
                        <details class="filter_accordion" open>
                            <summary class="filter_summary">
                                <span>Shop</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </summary>
                            <div class="filter_body">
                                <div class="filter_links">
                                    <a class="filter_link" href="{{ route($productsRoute) }}">All</a>
                                    <a class="filter_link" href="{{ route($productsRoute, ['discounted' => 1]) }}">Sale</a>
                                </div>
                            </div>
                        </details>

                        @foreach($categories as $cat)
                            <details class="filter_accordion">
                                <summary class="filter_summary">
                                    <span>{{ strtoupper($cat) }}</span>
                                    <i class="ri-arrow-down-s-line"></i>
                                </summary>
                                <div class="filter_body">
                                    <div class="filter_links">
                                        <a class="filter_link" href="{{ route($productsRoute, ['category' => $cat]) }}">All</a>
                                        @foreach(($subcategoriesByCategory[$cat] ?? collect()) as $sub)
                                            <a class="filter_link" href="{{ route($productsRoute, ['category' => $cat, 'subcategory' => $sub]) }}">{{ $sub }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </aside>

                    <section class="products_results">
                        <div class="products_header_row">
                            <h2 class="products_title">COLLECTION</h2>
                            <span class="products_count">{{ $groupLabel }}</span>
                        </div>

                        <div class="product_actions" style="gap: 14px; flex-wrap: wrap;">
                            <a class="vacancy_create_btn" href="{{ route($productsRoute) }}" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">SHOP ALL</a>
                            <a class="vacancy_create_btn" href="{{ route($productsRoute, ['discounted' => 1]) }}" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">SHOP SALE</a>
                        </div>
                    </section>
                </div>
            @endif
        </div>
    </section>
@endsection
