<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create()
    {
        return view('web.addresses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Adres başarıyla eklendi.']);
        }

        $redirectTo = $request->input('redirect_to');

        if ($redirectTo === 'checkout') {
            return redirect()->route('checkout.index')->with('success', 'Address added successfully.');
        }

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('success', 'Address added successfully.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);
        return view('web.addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Adres başarıyla güncellendi.']);
        }

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('success', 'Address updated successfully.');
    }

    public function destroy(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bu adres aktif siparişlerde kullanıldığı için silinemez.'
                ], 422);
            }
            return back()->with('error', 'Bu adres aktif siparişlerde kullanıldığı için silinemez.');
        }

        $address->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Adres başarıyla silindi.']);
        }

        return redirect()->route('profile.index', ['tab' => 'addresses'])->with('success', 'Address deleted successfully.');
    }

    protected function authorizeAddress(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
