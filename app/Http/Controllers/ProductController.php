<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('sizes')->orderBy('created_at', 'desc')->get();

        return response()->json($products);
    }

    public function browse(Request $request)
    {
        $group = (string) ($request->route('group') ?? $request->query('group', ''));
        $group = strtolower(trim($group));
        $groupLabel = $group === 'men' ? 'Men' : ($group === 'women' ? 'Women' : ($group === 'kids' ? 'Kids' : null));

        $query = Product::query()->with('sizes')->where('is_online', true);

        $category = (string) $request->query('category', '');
        $category = trim($category);

        $subcategory = (string) $request->query('subcategory', '');
        $subcategory = trim($subcategory);
        $sharedFallback = [
            'sneakers' => true,
            'boots' => true,
            'jeans' => true,
            't-shirts' => true,
            'sweaters' => true,
            'running' => true,
            'all sport' => true,
        ];
        $categoryFallbackMap = [
            'CLOTHING' => ['all clothing', 'shirts', 't-shirts', 'jeans', "cargo's", 'cargos', 'sweaters', 'hoodies', 'dresses', 'tops', 'skirts', 'jackets'],
            'SHOES' => ['all shoes', 'sneakers', 'boots', 'running', 'heels', 'running shoes', 'bike shoes'],
            'ACCESSORIES' => ['all accessories', 'bags', 'caps', 'watches', 'handbags', 'jewelry', 'scarves'],
            'BAGS & ACCESSORIES' => ['all accessories', 'bags', 'handbags', 'jewelry', 'scarves', 'watches', 'caps'],
            'SPORT' => ['all sport', 'training', 'running', 'football', 'gym', 'tracksuits', 'running shoes', 'bike shoes'],
            'BEAUTY' => ['all beauty', 'makeup', 'skincare', 'perfume'],
        ];

        if ($groupLabel) {
            $query->where(function ($q) use ($group, $groupLabel, $sharedFallback) {
                $q->where('taxonomy_group', $group)
                    ->orWhere(function ($qq) use ($groupLabel, $sharedFallback) {
                        $qq->whereNull('taxonomy_group')->where(function ($qqq) use ($groupLabel, $sharedFallback) {
                            $qqq->where('category', 'like', $groupLabel.' %');
                            foreach (array_keys($sharedFallback) as $val) {
                                $qqq->orWhereRaw('LOWER(category) = ?', [$val]);
                            }
                        });
                    });
            });
        }

        $categoryKey = strtoupper(trim($category));
        $categoryKey = preg_replace('/\s+/', ' ', $categoryKey ?? '');
        if ($categoryKey !== '') {
            $fallbackVals = $categoryFallbackMap[$categoryKey] ?? null;
            $query->where(function ($q) use ($categoryKey, $category, $fallbackVals) {
                $q->where('taxonomy_category', $categoryKey)
                    ->orWhere(function ($qq) use ($category) {
                        $qq->whereNull('taxonomy_category')
                            ->where(function ($qqq) use ($category) {
                                $qqq->where('category', 'like', '%'.$category.'%')
                                    ->orWhere('name', 'like', '%'.$category.'%');
                            });
                    });
                if (is_array($fallbackVals) && ! empty($fallbackVals)) {
                    $q->orWhere(function ($qq) use ($fallbackVals) {
                        $qq->whereNull('taxonomy_category')->whereIn(DB::raw('LOWER(category)'), $fallbackVals);
                    });
                }
            });
        }

        if ($subcategory !== '' && ! preg_match('/^all\s+/i', $subcategory)) {
            $query->where(function ($q) use ($subcategory) {
                $q->where('taxonomy_subcategory', $subcategory)
                    ->orWhere(function ($qq) use ($subcategory) {
                        $qq->whereNull('taxonomy_subcategory')->where(function ($qqq) use ($subcategory) {
                            $qqq->whereRaw('LOWER(category) = ?', [strtolower($subcategory)])
                                ->orWhere('name', 'like', '%'.$subcategory.'%')
                                ->orWhere('category', 'like', '%'.$subcategory.'%');
                        });
                    });
            });
        }

        $selectedSizes = $request->query('sizes', []);
        if (! is_array($selectedSizes)) {
            $selectedSizes = [];
        }
        $selectedSizes = array_values(array_filter(array_map('strval', $selectedSizes), fn ($x) => trim($x) !== ''));
        if (! empty($selectedSizes)) {
            $query->whereHas('sizes', function ($q) use ($selectedSizes) {
                $q->whereIn('size_label', $selectedSizes)->where('amount', '>', 0);
            });
        }

        $discounted = (string) $request->query('discounted', '');
        if ($discounted === '1' || strtolower($discounted) === 'true') {
            $query->where('discount_percent', '>', 0);
        }
        $selectedDiscounts = $request->query('discounts', []);
        if (! is_array($selectedDiscounts)) {
            $selectedDiscounts = [];
        }
        $selectedDiscounts = array_values(array_filter(array_map('intval', $selectedDiscounts), fn ($x) => $x > 0));
        if (! empty($selectedDiscounts)) {
            $query->whereIn('discount_percent', $selectedDiscounts);
        }

        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        $sort = (string) $request->query('sort', 'new');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(16)->appends($request->query());

        $sizesQuery = ProductSize::query()
            ->select('product_sizes.size_label')
            ->distinct()
            ->join('products', 'products.id', '=', 'product_sizes.product_id')
            ->where('product_sizes.amount', '>', 0);
        if ($groupLabel) {
            $sizesQuery->where(function ($q) use ($group, $groupLabel, $sharedFallback) {
                $q->where('products.taxonomy_group', $group)
                    ->orWhere(function ($qq) use ($groupLabel, $sharedFallback) {
                        $qq->whereNull('products.taxonomy_group')->where(function ($qqq) use ($groupLabel, $sharedFallback) {
                            $qqq->where('products.category', 'like', $groupLabel.' %');
                            foreach (array_keys($sharedFallback) as $val) {
                                $qqq->orWhereRaw('LOWER(products.category) = ?', [$val]);
                            }
                        });
                    });
            });
        }
        if ($categoryKey !== '') {
            $fallbackVals = $categoryFallbackMap[$categoryKey] ?? null;
            $sizesQuery->where(function ($q) use ($categoryKey, $category, $fallbackVals) {
                $q->where('products.taxonomy_category', $categoryKey)
                    ->orWhere(function ($qq) use ($category) {
                        $qq->whereNull('products.taxonomy_category')
                            ->where(function ($qqq) use ($category) {
                                $qqq->where('products.category', 'like', '%'.$category.'%')
                                    ->orWhere('products.name', 'like', '%'.$category.'%');
                            });
                    });
                if (is_array($fallbackVals) && ! empty($fallbackVals)) {
                    $q->orWhere(function ($qq) use ($fallbackVals) {
                        $qq->whereNull('products.taxonomy_category')->whereIn(DB::raw('LOWER(products.category)'), $fallbackVals);
                    });
                }
            });
        }
        if ($subcategory !== '' && ! preg_match('/^all\s+/i', $subcategory)) {
            $sizesQuery->where(function ($q) use ($subcategory) {
                $q->where('products.taxonomy_subcategory', $subcategory)
                    ->orWhere(function ($qq) use ($subcategory) {
                        $qq->whereNull('products.taxonomy_subcategory')->where(function ($qqq) use ($subcategory) {
                            $qqq->whereRaw('LOWER(products.category) = ?', [strtolower($subcategory)])
                                ->orWhere('products.name', 'like', '%'.$subcategory.'%')
                                ->orWhere('products.category', 'like', '%'.$subcategory.'%');
                        });
                    });
            });
        }

        $availableSizes = $sizesQuery->orderBy('product_sizes.size_label', 'asc')->pluck('product_sizes.size_label')->all();

        return view('products.index', [
            'products' => $products,
            'group' => $group,
            'groupLabel' => $groupLabel,
            'category' => $category,
            'subcategory' => $subcategory,
            'availableSizes' => $availableSizes,
            'selectedSizes' => $selectedSizes,
            'selectedDiscounts' => $selectedDiscounts,
            'discountedOnly' => ($discounted === '1' || strtolower($discounted) === 'true'),
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }

    public function showPublic(Product $product)
    {
        abort_unless((bool) ($product->is_online ?? true), 404);
        $ids = session()->get('recently_viewed_products', []);
        if (! is_array($ids)) {
            $ids = [];
        }
        $ids = array_values(array_filter(array_map('intval', $ids), fn ($x) => $x > 0));
        $ids = array_values(array_filter($ids, fn ($x) => $x !== (int) $product->id));
        array_unshift($ids, (int) $product->id);
        $ids = array_slice(array_values(array_unique($ids)), 0, 10);
        session()->put('recently_viewed_products', $ids);

        $recentIds = array_values(array_filter($ids, fn ($x) => $x !== (int) $product->id));
        $recent = collect();
        if (! empty($recentIds)) {
            $list = Product::query()
                ->where('is_online', true)
                ->whereIn('id', $recentIds)
                ->limit(3)
                ->get();
            $map = $list->keyBy('id');
            $recent = collect($recentIds)->map(fn ($id) => $map->get($id))->filter();
        }

        return view('products.show', ['product' => $product->load('sizes'), 'recentlyViewed' => $recent]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'taxonomy_group' => ['nullable', 'string', 'max:20'],
            'taxonomy_category' => ['nullable', 'string', 'max:255'],
            'taxonomy_subcategory' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.size_label' => ['required', 'string', 'max:50'],
            'sizes.*.size_type' => ['nullable', 'string', 'max:50'],
            'sizes.*.amount' => ['required', 'integer', 'min:0'],
            'is_online' => ['nullable', 'boolean'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $urls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $urls[] = Storage::url($path);
            }
        }

        $product = Product::create([
            'category' => $validated['category'],
            'taxonomy_group' => isset($validated['taxonomy_group']) ? strtolower(trim((string) $validated['taxonomy_group'])) : null,
            'taxonomy_category' => isset($validated['taxonomy_category']) ? strtoupper(trim((string) $validated['taxonomy_category'])) : null,
            'taxonomy_subcategory' => isset($validated['taxonomy_subcategory']) ? trim((string) $validated['taxonomy_subcategory']) : null,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'price' => $validated['price'] ?? 0,
            'description' => $validated['description'] ?? null,
            'images' => $urls,
            'is_online' => $validated['is_online'] ?? true,
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'created_by' => Auth::id(),
        ]);

        foreach (($validated['sizes'] ?? []) as $row) {
            ProductSize::create([
                'product_id' => $product->id,
                'size_label' => $row['size_label'],
                'size_type' => $row['size_type'] ?? null,
                'amount' => $row['amount'],
            ]);
        }

        return response()->json(['id' => $product->id, 'product' => $product->load('sizes')], 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.size_label' => ['required', 'string', 'max:50'],
            'sizes.*.size_type' => ['nullable', 'string', 'max:50'],
            'sizes.*.amount' => ['required', 'integer', 'min:0'],
            'is_online' => ['nullable', 'boolean'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        if (array_key_exists('name', $validated)) {
            $product->name = $validated['name'] ?? $product->name;
        }
        if (array_key_exists('description', $validated)) {
            $product->description = $validated['description'] ?? $product->description;
        }
        if (array_key_exists('price', $validated)) {
            $product->price = $validated['price'] ?? $product->price;
        }
        if (array_key_exists('is_online', $validated)) {
            $product->is_online = (bool) ($validated['is_online']);
        }
        if (array_key_exists('discount_percent', $validated)) {
            $product->discount_percent = (int) ($validated['discount_percent']);
        }

        $existing = is_array($product->images) ? $product->images : [];
        $newUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $newUrls[] = \Illuminate\Support\Facades\Storage::url($path);
            }
        }
        if (! empty($newUrls)) {
            $merged = array_slice(array_merge($existing, $newUrls), 0, 6);
            $product->images = $merged;
        }

        $product->save();

        if ($request->has('sizes') && is_array($request->input('sizes'))) {
            $incoming = collect($request->input('sizes'))
                ->filter(function ($row) {
                    return isset($row['size_label'], $row['amount']) && (int) ($row['amount']) > 0;
                })
                ->map(function ($row) {
                    return [
                        'size_label' => $row['size_label'],
                        'size_type' => $row['size_type'] ?? null,
                        'amount' => (int) $row['amount'],
                    ];
                })->values()->all();
            $keys = [];
            foreach ($incoming as $row) {
                $keys[] = $row['size_label'].'|'.($row['size_type'] ?? '');
                ProductSize::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'size_label' => $row['size_label'],
                        'size_type' => $row['size_type'],
                    ],
                    [
                        'amount' => $row['amount'],
                    ]
                );
            }
            $existingRows = ProductSize::where('product_id', $product->id)->get();
            foreach ($existingRows as $ex) {
                $key = $ex->size_label.'|'.($ex->size_type ?? '');
                if (! in_array($key, $keys, true)) {
                    $ex->delete();
                }
            }
        }

        return response()->json(['product' => $product->fresh('sizes')]);
    }

    public function destroy(Request $request, Product $product)
    {
        $images = is_array($product->images) ? $product->images : [];
        foreach ($images as $url) {
            $relative = ltrim(str_replace('/storage/', '', $url), '/');
            if ($relative) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relative);
            }
        }
        $product->delete();

        return response()->json(['deleted' => true]);
    }
}
