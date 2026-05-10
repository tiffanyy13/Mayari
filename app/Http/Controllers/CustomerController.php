<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    //home/shop
    public function home(Request $request)
    {
        $categories = Category::all();
        $query      = Product::active()->with('category');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('categoryID', $request->category);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('pName', 'like', "%{$term}%")
                  ->orWhere('descript', 'like', "%{$term}%");
            });
        }
        $products  = $query->get();
        $cartCount = array_sum(array_column(session()->get('cart', []), 'quantity'));
        $showAddressWelcome = (bool) $request->session()->pull('show_address_welcome', false);
        return view('customer.home', compact('products', 'categories', 'cartCount', 'showAddressWelcome'));
    }

    //cart
    public function cart()
    {
        $cartData  = session()->get('cart', []);
        $cartItems = [];
        $subtotal  = 0;

        foreach ($cartData as $productID => $item) {
            $product = Product::find($productID);
            if ($product) {
                $line = [
                    'productID' => $productID,
                    'pName'     => $product->pName,
                    'descript'  => $product->descript,
                    'variant'   => $item['variant'] ?? null,
                    'variants'  => is_array($product->variants) ? $product->variants : [],
                    'image'     => $product->image,
                    'unitPrice' => $product->price,
                    'quantity'  => $item['quantity'],
                    'stock'     => $product->stock,
                ];
                $cartItems[] = $line;
                $subtotal   += $product->price * $item['quantity'];
            }
        }

        $delivery = $subtotal > 0 ? 88.00 : 0;
        $total    = $subtotal + $delivery;
        $user     = Auth::user();

        $defaultAddress = $user ? $user->defaultShippingAddress()->first() : null;
        $hasAddresses = $user ? $user->shippingAddresses()->exists() : false;

        return view('customer.cart', compact('cartItems', 'subtotal', 'delivery', 'total', 'user', 'defaultAddress', 'hasAddresses'));
    }

    public function addToCart(Request $request, Product $product)
    {
        if ($product->stock <= 0) {
            return back()->with('error', 'This product is currently out of stock.');
        }

        $selectedVariant = trim((string) $request->input('variant', ''));
        $allowedVariants = is_array($product->variants) ? $product->variants : [];
        $hasVariants     = !empty($allowedVariants);

        if ($hasVariants && ($selectedVariant === '' || !in_array($selectedVariant, $allowedVariants, true))) {
            return back()->with('warning', 'Please select a valid shade or color before adding to cart.');
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$product->productID])) {
            $existingVariant = $cart[$product->productID]['variant'] ?? '';
            if ($existingVariant === $selectedVariant) {
                // FIX: cap quantity at available stock
                $newQty = $cart[$product->productID]['quantity'] + 1;
                if ($newQty > $product->stock) {
                    return back()->with('warning', "You've reached the maximum available stock for {$product->pName}.");
                }
                $cart[$product->productID]['quantity'] = $newQty;
            } else {
                $cart[$product->productID]['quantity'] = 1;
                $cart[$product->productID]['variant']  = $selectedVariant ?: null;
                session()->put('cart', $cart);
                return back()->with('warning', "{$product->pName} variant was updated in cart.");
            }
        } else {
            $cart[$product->productID] = [
                'quantity' => 1,
                'variant'  => $selectedVariant ?: null,
            ];
        }
        session()->put('cart', $cart);
        return back()->with('success', "{$product->pName} added to cart.");
    }

    public function updateCart(Request $request, Product $product)
    {
        $request->validate([
            // FIX: cap quantity at available stock
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $product->stock],
            'variant' => 'nullable|string',
        ]);

        $allowedVariants = is_array($product->variants) ? $product->variants : [];
        $hasVariants = !empty($allowedVariants);
        $selectedVariant = trim((string) $request->input('variant', ''));

        if ($hasVariants && ($selectedVariant === '' || !in_array($selectedVariant, $allowedVariants, true))) {
            return back()->with('warning', 'Please select a valid shade or color.');
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$product->productID])) {
            $cart[$product->productID]['quantity'] = $request->quantity;
            $cart[$product->productID]['variant'] = $hasVariants ? $selectedVariant : null;
            session()->put('cart', $cart);

            if ($request->input('updateType') === 'variant') {
                return back()->with('success', 'Item Color/Shade updated.');
            }

            return back()->with('success', 'Cart quantity updated.');
        }
        return back()->with('warning', 'Item not found in cart.');
    }

    public function removeFromCart(Product $product)
    {
        $cart = session()->get('cart', []);
        if (!isset($cart[$product->productID])) {
            return back()->with('warning', 'Item not found in cart.');
        }
        unset($cart[$product->productID]);
        session()->put('cart', $cart);
        return back()->with('error', "{$product->pName} removed from cart.");
    }

    //place order
    public function placeOrder(Request $request)
    {
        $request->validate([
            'street'        => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'province'      => 'required|string|max:100',
            'country'       => 'required|string|max:100',
            'postal'        => 'nullable|string|max:20',
            'paymentMethod' => 'required|in:cod,ecard',
            'gcashName'       => 'nullable|string|max:100|required_if:paymentMethod,ecard',
            'gcashNumber'     => 'nullable|digits:11|required_if:paymentMethod,ecard',
            // FIX: allow 12–14 digits to cover real GCash reference number lengths
            'referenceNumber' => 'nullable|digits_between:12,14|required_if:paymentMethod,ecard',
        ]);

        $cartData = session()->get('cart', []);
        if (empty($cartData)) {
            return back()->with('error', 'Your cart is empty.');
        }

        // FIX: wrap everything in a DB transaction so partial failures don't leave orphaned orders
        try {
            $order = DB::transaction(function () use ($request, $cartData) {

                // FIX: check stock for every item before creating anything
                foreach ($cartData as $productID => $item) {
                    $product = Product::lockForUpdate()->find($productID);
                    if (!$product || $product->stock < $item['quantity']) {
                        $name = $product->pName ?? "Product #{$productID}";
                        throw new \Exception("Sorry, \"{$name}\" no longer has enough stock. Please update your cart.");
                    }
                }

                $subtotal = 0;
                foreach ($cartData as $productID => $item) {
                    $product   = Product::find($productID);
                    $subtotal += $product ? $product->price * $item['quantity'] : 0;
                }
                $delivery = 88.00;
                $total    = $subtotal + $delivery;

                $order = Order::create([
                    'userID'          => Auth::id(),
                    'deliveryAdd'     => $request->street,
                    'city'            => $request->city,
                    'province'        => $request->province,
                    'country'         => $request->country,
                    'postal'          => $request->postal ?? '',
                    'contactNo'       => Auth::user()?->phone,
                    'paymentMethod'   => $request->paymentMethod,
                    'gcashName'       => $request->paymentMethod === 'ecard' ? $request->gcashName : null,
                    'gcashNumber'     => $request->paymentMethod === 'ecard' ? $request->gcashNumber : null,
                    'referenceNumber' => $request->paymentMethod === 'ecard' ? $request->referenceNumber : null,
                    'amountPaid'      => $request->paymentMethod === 'ecard' ? $total : null,
                    'paymentStatus'   => $request->paymentMethod === 'ecard' ? 'Paid' : null,
                    'deliveryFee'     => $delivery,
                    'subtotal'        => $subtotal,
                    'total'           => $total,
                    'status'          => 'Pending',
                ]);

                foreach ($cartData as $productID => $item) {
                    $product = Product::find($productID);
                    if ($product) {
                        OrderItem::create([
                            'orderID'   => $order->orderID,
                            'productID' => $productID,
                            'quantity'  => $item['quantity'],
                            'unitPrice' => $product->price,
                        ]);
                        // FIX: decrement stock now that the order item is saved
                        $product->decrement('stock', $item['quantity']);
                    }
                }

                return $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->forget('cart');

        $user = Auth::user();
        if ($user && !$user->shippingAddresses()->exists()) {
            session()->put('prompt_save_address', true);
            session()->put('post_order_address', [
                'fullName'    => $user->fullName,
                'phone'       => $user->phone,
                'addressLine' => $request->street,
                'city'        => $request->city,
                'province'    => $request->province,
                'country'     => $request->country,
                'postal'      => $request->postal,
            ]);
        }

        return redirect()->route('customer.order.placed')->with('success', 'Order placed successfully!');
    }

    public function orderPlaced()
    {
        $promptSaveAddress = (bool) session()->pull('prompt_save_address', false);
        $postOrderAddress = session()->pull('post_order_address', null);
        return view('customer.order-placed', compact('promptSaveAddress', 'postOrderAddress'));
    }

    //my orders
    public function orders()
    {
        $orders = Order::with(['items.product'])
            ->where('userID', Auth::id())
            ->latest('createdAt')
            ->get();
        return view('customer.orders', compact('orders'));
    }

    //profile
    public function profile()
    {
        $user = Auth::user();
        $addresses = $user->shippingAddresses()->get();
        $defaultAddress = $user->defaultShippingAddress()->first() ?: $addresses->first();
        $openAddress = request()->boolean('openAddress', false);
        $redirect = request()->query('redirect');
        return view('customer.profile', compact('user', 'addresses', 'defaultAddress', 'openAddress', 'redirect'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate(
            [
            'firstName'   => 'required|string|max:100',
            'lastName'    => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $user->userID . ',userID',
            'phone'       => ['nullable', 'string', 'size:11', 'regex:/^09[0-9]{9}$/'],
            'password'    => 'nullable|string|min:6|confirmed',
            'shipping_addressLine' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_province' => 'nullable|string|max:100',
            'shipping_country' => 'nullable|string|max:100',
            'shipping_postal' => 'nullable|string|max:20',
            'shipping_label' => 'nullable|string|max:30',
            ],
            [
                'phone.size'  => 'Mobile number must be exactly 11 digits (e.g. 09171234567).',
                'phone.regex' => 'Use a Philippine mobile number: 11 digits starting with 09 (e.g. 09171234567).',
            ]
        );

        $data = [
            'firstName'   => $request->firstName,
            'lastName'    => $request->lastName,
            'email'       => $request->email,
            'phone' => $request->phone,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $anyShipping = collect([
            $request->input('shipping_addressLine'),
            $request->input('shipping_city'),
            $request->input('shipping_province'),
            $request->input('shipping_country'),
            $request->input('shipping_postal'),
            $request->input('shipping_label'),
        ])->filter(function ($v) {
            return $v !== null && trim((string) $v) !== '';
        })->isNotEmpty();

        if ($anyShipping) {
            $request->validate([
                'shipping_addressLine' => 'required|string|max:255',
                'shipping_city' => 'required|string|max:100',
                'shipping_province' => 'required|string|max:100',
                'shipping_country' => 'required|string|max:100',
                'shipping_postal' => 'nullable|string|max:20',
                'shipping_label' => 'required|string|max:30',
            ]);

            $existing = $user->defaultShippingAddress()->first() ?: $user->shippingAddresses()->first();
            ShippingAddress::where('userID', $user->userID)->update(['isDefault' => false]);

            if ($existing) {
                $existing->update([
                    'fullName' => $user->fullName,
                    'phone' => (string) ($user->phone ?? ''),
                    'addressLine' => $request->shipping_addressLine,
                    'city' => $request->shipping_city,
                    'province' => $request->shipping_province,
                    'country' => $request->shipping_country,
                    'postal' => $request->shipping_postal,
                    'label' => $request->shipping_label,
                    'isDefault' => true,
                ]);
            } else {
                ShippingAddress::create([
                    'userID' => $user->userID,
                    'fullName' => $user->fullName,
                    'phone' => (string) ($user->phone ?? ''),
                    'addressLine' => $request->shipping_addressLine,
                    'city' => $request->shipping_city,
                    'province' => $request->shipping_province,
                    'country' => $request->shipping_country,
                    'postal' => $request->shipping_postal,
                    'label' => $request->shipping_label,
                    'isDefault' => true,
                ]);
            }
        }

        return back()->with('success', 'Profile updated successfully!');
    }
}