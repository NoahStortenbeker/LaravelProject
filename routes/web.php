<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| Web Routes - FootLab E-Commerce
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    $products = \App\Models\Product::query()
        ->orderBy('created_at', 'desc')
        ->get();

    return view('home', ['products' => $products]);
})->name('home');

// Product Routes (Public)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'browse'])->name('index');
    Route::get('/men', [ProductController::class, 'browse'])->defaults('group', 'men')->name('men');
    Route::get('/women', [ProductController::class, 'browse'])->defaults('group', 'women')->name('women');
    Route::get('/kids', [ProductController::class, 'browse'])->defaults('group', 'kids')->name('kids');

    Route::get('/{product}', [ProductController::class, 'showPublic'])->name('show');
});

// Collections Routes (Public)
Route::prefix('collections')->name('collections.')->group(function () {
    Route::get('/', function () {
        return view('collections.index');
    })->name('index');

    Route::get('/{slug}', function ($slug) {
        $slug = strtolower(trim((string) $slug));

        if (in_array($slug, ['men', 'women', 'kids'], true)) {
            return redirect()->route('products.'.$slug);
        }

        if ($slug === 'sale') {
            return redirect()->route('products.index', ['discounted' => 1]);
        }

        return redirect()->route('collections.index');
    })->name('show');
});

// Vacancies (Public API)
Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies.index');
// Products (Public API)
Route::get('/api/products', [ProductController::class, 'index'])->name('products.api');
// Countries (Public API for address form)
Route::get('/api/countries', function () {
    $countries = [
        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi',
        'Cabo Verde', 'Cambodia', 'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo, Democratic Republic', 'Congo, Republic', 'Costa Rica', 'Cote d’Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czechia',
        'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic',
        'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia',
        'Fiji', 'Finland', 'France',
        'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
        'Haiti', 'Honduras', 'Hungary',
        'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy',
        'Jamaica', 'Japan', 'Jordan',
        'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan',
        'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
        'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway',
        'Oman',
        'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal',
        'Qatar',
        'Romania', 'Russia', 'Rwanda',
        'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria',
        'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu',
        'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan',
        'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam',
        'Yemen',
        'Zambia', 'Zimbabwe',
    ];

    return response()->json($countries);
})->name('countries.api');

// Profile Routes (Authenticated Users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wishlist -> redirect to Account panel
    Route::get('/wishlist', function () {
        return redirect()->route('users.dashboard', ['panel' => 'wishlist']);
    })->name('wishlist.index');

    // Orders (Purchases) -> redirect to Account panel
    Route::get('/orders', function () {
        return redirect()->route('users.dashboard', ['panel' => 'orders']);
    })->name('orders.index');

    // Returns -> redirect to Account panel (orders)
    Route::get('/returns', function () {
        return redirect()->route('users.dashboard', ['panel' => 'orders']);
    })->name('returns.index');

    // User dashboard
    Route::get('/account', function () {
        $orders = \App\Models\Order::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('users.dashboard-users', [
            'orders' => $orders,
        ]);
    })->name('users.dashboard');

    Route::get('/dashboard', function () {
        return redirect()->route('users.dashboard');
    })->name('dashboard');

    Route::patch('/account', [AccountController::class, 'update'])->name('users.update');
    Route::patch('/account/address', [AccountController::class, 'updateAddress'])->name('users.address.update');
    Route::post('/account/photo', [AccountController::class, 'updatePhoto'])->name('users.photo');
    Route::post('/account/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('users.orders.cancel');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/payment/{order}', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Heartbeat for presence tracking
    Route::post('/heartbeat', function () {
        if (\Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Auth::user()->forceFill(['last_seen_at' => now()])->save();
        }

        return response()->noContent();
    })->name('heartbeat');

    // Admin dashboard (only via click when logged in as admin)
    Route::get('/admin', function () {
        return view('admin.dashboard-admin');
    })->middleware(\App\Http\Middleware\IsAdmin::class)->name('admin.dashboard');
    Route::patch('/admin/orders/{order}', function (\Illuminate\Http\Request $request, \App\Models\Order $order) {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['pending_payment', 'pending', 'processing', 'paid', 'completed', 'canceling', 'canceled', 'cancelled']),
            ],
        ]);

        $next = strtolower($validated['status']);
        $next = match ($next) {
            'pending' => 'pending_payment',
            'paid' => 'processing',
            'cancelled' => 'canceled',
            default => $next,
        };

        $order->forceFill(['status' => $next])->save();

        return redirect()->route('admin.dashboard', ['panel' => 'orders'])->with('admin_order_updated', true);
    })->middleware(\App\Http\Middleware\IsAdmin::class)->name('admin.orders.update');
    Route::post('/admin/vacancies', [VacancyController::class, 'store'])->middleware(\App\Http\Middleware\IsAdmin::class)->name('admin.vacancies.store');
    Route::post('/admin/products', [ProductController::class, 'store'])->middleware(\App\Http\Middleware\IsAdmin::class)->name('admin.products.store');
    Route::patch('/admin/products/{product}', [ProductController::class, 'update'])->middleware(\App\Http\Middleware\IsAdmin::class)->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->middleware(\App\Http\Middleware\IsAdmin::class)->name('admin.products.destroy');
});

// Auth routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';
