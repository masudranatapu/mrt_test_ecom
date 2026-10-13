<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ShopException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\AddToCartRequest;
use App\Http\Requests\Frontend\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index(): View
    {
        $items = $this->cart->items();

        return view('frontend.cart.index', [
            'items' => $items,
            'totals' => $this->cart->totals($items),
        ]);
    }

    public function store(AddToCartRequest $request): JsonResponse|RedirectResponse
    {
        // Prices hidden = buying not allowed yet (guest or not yet approved)
        if (! can_see_price()) {
            $message = auth('customer')->check()
                ? 'Your account must be approved before you can order.'
                : 'Please log in to add products to your cart.';

            return $this->respond($request, false, $message, auth('customer')->check() ? null : route('customer.login'));
        }

        $product = Product::findOrFail($request->integer('product_id'));

        try {
            $this->cart->add($product, $request->integer('quantity', 1));
        } catch (ShopException $e) {
            return $this->respond($request, false, $e->getMessage());
        }

        return $this->respond($request, true, "{$product->name} added to your cart.");
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): JsonResponse|RedirectResponse
    {
        try {
            $this->cart->update($item->load('product'), $request->integer('quantity'));
        } catch (ShopException $e) {
            return $this->respond($request, false, $e->getMessage());
        }

        return $this->respond($request, true, 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $item): JsonResponse|RedirectResponse
    {
        abort_unless($this->cart->owns($item), 403);

        $this->cart->remove($item);

        return $this->respond($request, true, 'Item removed from your cart.');
    }

    /**
     * JSON for AJAX calls (header counter + toast), redirect otherwise.
     */
    private function respond(Request $request, bool $ok, string $message, ?string $redirect = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'cart_count' => $this->cart->count(),
                'redirect' => $redirect,
            ], $ok ? 200 : 422);
        }

        if ($redirect) {
            return redirect($redirect)->with('warning', $message);
        }

        return back()->with($ok ? 'success' : 'error', $message);
    }
}
