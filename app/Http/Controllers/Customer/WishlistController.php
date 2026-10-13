<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreWishlistRequest;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlists = $request->user('customer')->wishlists()
            ->whereHas('product', fn ($q) => $q->active())
            ->with('product.category')
            ->paginate(12);

        return view('customer.wishlist.index', compact('wishlists'));
    }

    /**
     * Toggle: adds the product, or removes it if it is already saved.
     */
    public function store(StoreWishlistRequest $request): JsonResponse|RedirectResponse
    {
        $customer = $request->user('customer');
        $existing = $customer->wishlists()->where('product_id', $request->product_id)->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            $customer->wishlists()->create(['product_id' => $request->product_id]);
            $added = true;
        }

        $message = $added ? 'Saved to your wishlist.' : 'Removed from your wishlist.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'added' => $added,
                'message' => $message,
                'wishlist_count' => $customer->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, Wishlist $wishlist): RedirectResponse
    {
        abort_unless($wishlist->customer_id === $request->user('customer')->id, 403);

        $wishlist->delete();

        return back()->with('success', 'Removed from your wishlist.');
    }
}
