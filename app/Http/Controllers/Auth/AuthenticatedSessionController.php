<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $guestCartId = $request->session()->get('cart_id');

        $request->authenticate();

        $request->session()->regenerate();

        $this->mergeCartOnLogin($request->user(), $guestCartId);

        return redirect(RouteServiceProvider::HOME);
    }

    protected function mergeCartOnLogin($user, ?int $guestCartId): void
    {
        if (!$guestCartId) {
            return;
        }

        $guestCart = \App\Models\Cart::where('id', $guestCartId)
            ->whereNull('user_id')
            ->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = $this->cartService->getOrCreateCart($user->id);

        $this->cartService->mergeCarts($guestCart, $userCart);

        // Remove from session once merged
        session()->forget('cart_id');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
