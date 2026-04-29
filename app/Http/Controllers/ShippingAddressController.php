<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('customer.profile', [
            'openAddress' => $request->boolean('open', false) ? 1 : null,
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'fullName' => 'required|string|max:160',
            'phone' => 'required|string|max:20',
            'addressLine' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal' => 'nullable|string|max:20',
            'landmark' => 'nullable|string|max:160',
            'label' => 'required|string|max:30',
            'makeDefault' => 'nullable|boolean',
            'redirect' => 'nullable|string|max:255',
        ]);

        $hasAny = $user->shippingAddresses()->exists();
        $makeDefault = !$hasAny || (bool) ($request->input('makeDefault'));

        if ($makeDefault) {
            ShippingAddress::where('userID', $user->userID)->update(['isDefault' => false]);
        }

        ShippingAddress::create([
            'userID' => $user->userID,
            'fullName' => $data['fullName'],
            'phone' => $data['phone'],
            'addressLine' => $data['addressLine'],
            'city' => $data['city'],
            'province' => $data['province'],
            'country' => $data['country'],
            'postal' => $data['postal'] ?? null,
            'landmark' => $data['landmark'] ?? null,
            'label' => $data['label'],
            'isDefault' => $makeDefault,
        ]);

        $target = $this->safeRedirectTarget($request->input('redirect'));
        return redirect($target)->with('success', 'Address saved successfully! It will now appear automatically in your checkout.');
    }

    public function update(Request $request, ShippingAddress $address)
    {
        $user = Auth::user();
        if ($address->userID !== $user->userID) {
            abort(403);
        }

        $data = $request->validate([
            'fullName' => 'required|string|max:160',
            'phone' => 'required|string|max:20',
            'addressLine' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal' => 'nullable|string|max:20',
            'landmark' => 'nullable|string|max:160',
            'label' => 'required|string|max:30',
            'makeDefault' => 'nullable|boolean',
            'redirect' => 'nullable|string|max:255',
        ]);

        $makeDefault = (bool) ($request->input('makeDefault'));
        if ($makeDefault) {
            ShippingAddress::where('userID', $user->userID)->update(['isDefault' => false]);
        }

        $address->update([
            'fullName' => $data['fullName'],
            'phone' => $data['phone'],
            'addressLine' => $data['addressLine'],
            'city' => $data['city'],
            'province' => $data['province'],
            'country' => $data['country'],
            'postal' => $data['postal'] ?? null,
            'landmark' => $data['landmark'] ?? null,
            'label' => $data['label'],
            'isDefault' => $makeDefault ? true : $address->isDefault,
        ]);

        $target = $this->safeRedirectTarget($request->input('redirect'));
        return redirect($target)->with('success', 'Address updated successfully.');
    }

    public function destroy(Request $request, ShippingAddress $address)
    {
        $user = Auth::user();
        if ($address->userID !== $user->userID) {
            abort(403);
        }

        $wasDefault = (bool) $address->isDefault;
        $address->delete();

        if ($wasDefault) {
            $next = ShippingAddress::where('userID', $user->userID)->latest('createdAt')->first();
            if ($next) {
                $next->update(['isDefault' => true]);
            }
        }

        $target = $this->safeRedirectTarget($request->input('redirect'));
        return redirect($target)->with('success', 'Address deleted.');
    }

    public function makeDefault(Request $request, ShippingAddress $address)
    {
        $user = Auth::user();
        if ($address->userID !== $user->userID) {
            abort(403);
        }

        ShippingAddress::where('userID', $user->userID)->update(['isDefault' => false]);
        $address->update(['isDefault' => true]);

        $target = $this->safeRedirectTarget($request->input('redirect'));
        return redirect($target)->with('success', 'Default address updated.');
    }

    private function safeRedirectTarget(?string $target): string
    {
        if (!$target) return route('customer.home');
        if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) return route('customer.home');
        if (!str_starts_with($target, '/')) return route('customer.home');
        return $target;
    }
}

