<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('checkout.index', [
            'user' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['nullable', 'string', 'max:40'],
            'shipping_address_line' => ['required', 'string', 'max:255'],
            'shipping_zip_code' => ['required', 'string', 'max:16'],
            'shipping_city' => ['required', 'string', 'max:128'],
            'shipping_country' => ['required', 'string', 'max:128'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'cart_json' => ['required', 'string'],
        ]);

        $cart = $this->parseCartJson($validated['cart_json']);
        if (empty($cart)) {
            return back()->withErrors(['cart_json' => 'Your cart is empty.'])->withInput();
        }

        $productIds = array_values(array_unique(array_map(fn ($row) => (int) $row['id'], $cart)));
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_online', true)
            ->get()
            ->keyBy('id');

        foreach ($cart as $row) {
            if (! isset($products[(int) $row['id']])) {
                return back()->withErrors(['cart_json' => 'One or more products are unavailable.'])->withInput();
            }
        }

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $validated, $cart, $products) {
            $items = [];
            $subtotal = 0.0;

            foreach ($cart as $row) {
                $product = $products[(int) $row['id']];
                $qty = (int) $row['qty'];
                $unit = $this->finalPriceForProduct($product);
                $line = $unit * $qty;
                $subtotal += $line;

                $img = is_array($product->images) && count($product->images) ? (string) $product->images[0] : (string) ($product->image ?? '');

                $items[] = [
                    'product_id' => $product->id,
                    'name' => (string) ($product->name ?? ''),
                    'image' => $img !== '' ? $img : null,
                    'category' => (string) ($product->category ?? ''),
                    'qty' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $line,
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending_payment',
                'currency' => 'EUR',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'shipping_name' => $validated['shipping_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'] ?? null,
                'shipping_address_line' => $validated['shipping_address_line'],
                'shipping_zip_code' => $validated['shipping_zip_code'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_country' => $validated['shipping_country'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $row) {
                $row['order_id'] = $order->id;
                OrderItem::create($row);
            }

            return $order;
        });

        return redirect()->route('checkout.payment', ['order' => $order->id]);
    }

    public function payment(Request $request, Order $order): View
    {
        $this->authorizeOrderAccess($request, $order);

        $order->load('items');

        return view('checkout.payment', [
            'order' => $order,
        ]);
    }

    public function pay(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrderAccess($request, $order);

        if ($order->status !== 'pending_payment') {
            return redirect()->route('checkout.confirmation', ['order' => $order->id]);
        }

        DB::transaction(function () use ($order) {
            $order->forceFill([
                'status' => 'processing',
                'paid_at' => now(),
                'payment_reference' => 'test-'.$order->id.'-'.now()->format('YmdHis'),
            ])->save();
        });

        Mail::to($order->shipping_email ?: $request->user()->email)->send(new OrderConfirmed($order->fresh('items')));

        return redirect()->route('checkout.confirmation', ['order' => $order->id]);
    }

    public function confirmation(Request $request, Order $order): View
    {
        $this->authorizeOrderAccess($request, $order);

        $order->load('items');

        return view('checkout.confirmation', [
            'order' => $order,
        ]);
    }

    private function authorizeOrderAccess(Request $request, Order $order): void
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 404);
    }

    private function parseCartJson(string $raw): array
    {
        try {
            $decoded = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }

        if (! is_array($decoded)) {
            return [];
        }

        $items = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = isset($row['id']) ? (int) $row['id'] : 0;
            $qty = isset($row['qty']) ? (int) $row['qty'] : 0;
            if ($id <= 0 || $qty <= 0) {
                continue;
            }
            $items[] = ['id' => $id, 'qty' => min(99, $qty)];
        }

        return $items;
    }

    private function finalPriceForProduct(Product $product): float
    {
        $base = (float) ($product->price ?? 0);
        $disc = (int) ($product->discount_percent ?? 0);
        if ($disc > 0) {
            return max(0, $base * (1 - $disc / 100));
        }

        return max(0, $base);
    }
}
