<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.variant.product.images', 'shipment'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $addresses = Address::where('user_id', $user->id)->get();

        return view('web.profile.index', compact('user', 'orders', 'addresses'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $isEmailChanged = $user->email !== $validated['email'];

        $user->update($validated);

        if ($isEmailChanged) {
            $user->email_verified_at = null;
            $user->save();
        }

        return response()->json(['success' => true, 'message' => 'Profil bilgileriniz güncellendi.']);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mevcut şifreniz yanlış.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['success' => true, 'message' => 'Şifreniz başarıyla değiştirildi.']);
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:500',
            'address_line2' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();

        if (!empty($validated['is_default'])) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create($validated);

        return response()->json(['success' => true, 'message' => 'Adres başarıyla eklendi.']);
    }

    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:500',
            'address_line2' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json(['success' => true, 'message' => 'Adres başarıyla güncellendi.']);
    }

    public function destroyAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $hasActiveOrders = \App\Models\Order::where('address_id', $address->id)
            ->where(function ($query) {
                $query
                    ->whereIn('fulfillment_status', ['pending', 'processing', 'packed', 'shipped', 'out_for_delivery'])
                    ->orWhere(function ($legacyQuery) {
                        $legacyQuery
                            ->whereNull('fulfillment_status')
                            ->whereIn('status', ['pending', 'processing', 'shipped', 'paid']);
                    });
            })
            ->exists();

        if (!$hasActiveOrders) {
            $hasActiveOrders = \App\Models\Order::where('user_id', Auth::id())
                ->where('shipping_phone', $address->phone)
                ->where('shipping_address_line1', $address->address_line1)
                ->where('shipping_city', $address->city)
                ->where(function ($query) {
                    $query
                        ->whereIn('fulfillment_status', ['pending', 'processing', 'packed', 'shipped', 'out_for_delivery'])
                        ->orWhere(function ($legacyQuery) {
                            $legacyQuery
                                ->whereNull('fulfillment_status')
                                ->whereIn('status', ['pending', 'processing', 'shipped', 'paid']);
                        });
                })
                ->exists();
        }
        
        if ($hasActiveOrders) {
            return response()->json([
                'success' => false, 
                'message' => 'Bu adres aktif siparişlerde kullanıldığı için silinemez.'
            ], 422);
        }

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Adres başarıyla silindi.']);
    }

    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json(['success' => true, 'message' => 'Varsayılan adres değiştirildi.']);
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Şifreniz yanlış.'
            ], 422);
        }

        $hasActiveOrders = \App\Models\Order::where('user_id', $userId)
            ->where(function ($query) {
                $query
                    ->whereIn('fulfillment_status', ['pending', 'processing', 'packed', 'shipped', 'out_for_delivery'])
                    ->orWhere(function ($legacyQuery) {
                        $legacyQuery
                            ->whereNull('fulfillment_status')
                            ->whereIn('status', ['pending', 'processing', 'shipped', 'paid']);
                    });
            })
            ->exists();

        if ($hasActiveOrders) {
            return response()->json([
                'success' => false,
                'message' => 'Aktif siparişleriniz olduğu için hesabınız silinemez. Lütfen önce siparişlerinizi iptal edin veya teslim alın.'
            ], 422);
        }

        Auth::logout();
        
        \App\Models\Order::where('user_id', $userId)->update(['user_id' => null]);
        \Illuminate\Support\Facades\DB::table('users')->where('id', $userId)->delete();

        return response()->json(['success' => true, 'message' => 'Hesabınız başarıyla silindi.']);
    }
}
