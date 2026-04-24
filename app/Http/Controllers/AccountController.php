<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Update the user's profile information from the account dashboard.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('users.dashboard')->with('status', 'profile-updated');
    }

    /**
     * Update only the user's address fields from the Address panel.
     */
    public function updateAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address_line' => ['sometimes', 'nullable', 'string', 'max:255'],
            'zip_code' => ['sometimes', 'nullable', 'string', 'max:16'],
            'city' => ['sometimes', 'nullable', 'string', 'max:128'],
            'country' => ['sometimes', 'nullable', 'string', 'max:128'],
        ]);

        $user = $request->user();
        $user->fill($validated);
        $user->save();

        return Redirect::route('users.dashboard', ['panel' => 'address'])->with('status', 'profile-updated');
    }

    public function updatePhoto(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'profile-updated',
                'photo_url' => $user->profile_photo_url,
            ]);
        }

        return Redirect::route('users.dashboard')->with('status', 'profile-updated');
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 404);

        $raw = strtolower((string) ($order->status ?? ''));
        $blocked = in_array($raw, ['completed', 'canceling', 'cancelled', 'canceled'], true);
        if ($blocked) {
            return Redirect::route('users.dashboard', ['panel' => 'orders'])
                ->with('order_action', 'This order can’t be cancelled anymore.');
        }

        $order->forceFill(['status' => 'canceling'])->save();

        return Redirect::route('users.dashboard', ['panel' => 'orders'])
            ->with('order_action', 'Order #'.$order->id.' is canceling.');
    }
}
