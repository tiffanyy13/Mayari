<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $now = now('Asia/Manila');

        $salesToday = Order::whereDate('createdAt', $now->toDateString())
            ->whereNotIn('status', ['Canceled'])->sum('total');
        $salesWeek  = Order::whereBetween('createdAt', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->whereNotIn('status', ['Canceled'])->sum('total');
        $salesMonth = Order::whereYear('createdAt', $now->year)
            ->whereMonth('createdAt', $now->month)
            ->whereNotIn('status', ['Canceled'])->sum('total');

        $totalOrders    = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $ordersToday    = Order::whereDate('createdAt', $now->toDateString())->count();
        $ordersThisMonth = Order::whereYear('createdAt', $now->year)
            ->whereMonth('createdAt', $now->month)
            ->count();
        $newCustomersThisMonth = User::where('role', 'customer')
            ->whereYear('createdAt', $now->year)
            ->whereMonth('createdAt', $now->month)
            ->count();

        $lowStock = Product::active()
            ->where('stock', '<=', 9)
            ->orderBy('stock')
            ->get();

        $recentOrders = Order::with('user')->latest('createdAt')->take(8)->get();

        $trendStart = $now->copy()->subDays(13)->startOfDay();
        $trendEnd = $now->copy()->endOfDay();
        $rawTrend = Order::whereBetween('createdAt', [$trendStart, $trendEnd])
            ->whereNotIn('status', ['Canceled'])
            ->selectRaw('DATE(createdAt) as order_date, SUM(total) as total')
            ->groupBy('order_date')
            ->pluck('total', 'order_date');

        $salesGraph = collect(range(13, 0))->map(function ($daysAgo) use ($now, $rawTrend) {
            $date = $now->copy()->subDays($daysAgo)->toDateString();
            return ['date' => $date, 'total' => (float) ($rawTrend[$date] ?? 0)];
        });

        return view('admin.dashboard', compact(
            'salesToday', 'salesWeek', 'salesMonth',
            'totalOrders', 'totalCustomers',
            'ordersToday', 'ordersThisMonth', 'newCustomersThisMonth',
            'lowStock', 'recentOrders', 'salesGraph'
        ));
    }

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'items.product'])->latest('createdAt');
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', ucfirst($request->status));
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('orderID', 'like', "%{$term}%")
                  ->orWhereHas('user', fn($u) =>
                      $u->where('firstName', 'like', "%{$term}%")
                        ->orWhere('lastName', 'like', "%{$term}%"));
            });
        }
        $orders    = $query->paginate(5)->withQueryString();
        $analytics = $this->orderAnalytics();
        return view('admin.manage-orders', compact('orders', 'analytics'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:Pending,Accepted,Shipped,Delivered,Canceled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', "Order #{$order->orderID} updated to {$request->status}.");
    }

    public function products()
    {
        $products   = Product::active()->with('category')->orderBy('pName')->paginate(10)->withQueryString();
        $categories = Category::all();
        return view('admin.manage-products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pName'      => 'required|string|max:150',
            'categoryID' => 'required|exists:categories,categoryID',
            'price'      => 'required|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'descript'   => 'required|string',
            'variants'   => 'nullable|string|max:500',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url'  => 'nullable|string|max:2048|url',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('openAdd', true);
        }
        $data = $validator->validated();
        unset($data['image'], $data['image_url']);
        $data['variants'] = $this->normalizeVariants($request->input('variants'));
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('images/products'), $filename);
            $data['image'] = 'images/products/' . $filename;
        } elseif ($request->filled('image_url')) {
            $data['image'] = trim((string) $request->input('image_url'));
        } else {
            $data['image'] = 'example.image';
        }
        $data['isArchived'] = false;
        Product::create($data);
        return back()->with('success', 'Product added.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'pName'      => 'required|string|max:150',
            'categoryID' => 'required|exists:categories,categoryID',
            'price'      => 'required|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'descript'   => 'required|string',
            'variants'   => 'nullable|string|max:500',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url'  => 'nullable|string|max:2048|url',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('openEdit', $product->productID);
        }
        $data = $validator->validated();
        unset($data['image'], $data['image_url']);
        $data['variants'] = $this->normalizeVariants($request->input('variants'));
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('images/products'), $filename);
            $this->deleteLocalProductImageIfAny($product->image);
            $data['image'] = 'images/products/' . $filename;
        } elseif ($request->filled('image_url')) {
            $this->deleteLocalProductImageIfAny($product->image);
            $data['image'] = trim((string) $request->input('image_url'));
        }
        $product->update($data);
        return back()->with('success', 'Product updated.');
    }

    public function archiveProduct(Product $product)
    {
        $product->update(['isArchived' => true]);
        return back()->with('success', "{$product->pName} archived.");
    }

    public function unarchiveProduct(Product $product)
    {
        $product->update(['isArchived' => false]);
        return back()->with('success', "{$product->pName} restored.");
    }

    public function archived()
    {
        $products = Product::archived()->with('category')->orderBy('pName')->paginate(10)->withQueryString();

        return view('admin.archived', compact('products'));
    }

    public function customers(Request $request)
    {
        $query = User::where('role', 'customer')->withCount('orders');
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('firstName', 'like', "%{$term}%")
                  ->orWhere('lastName', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }
        $customers = $query->latest()->paginate(5)->withQueryString();
        return view('admin.customers', compact('customers'));
    }

    public function reports(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from, 'Asia/Manila')->startOfDay()
            : now('Asia/Manila')->startOfMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to, 'Asia/Manila')->endOfDay()
            : now('Asia/Manila')->endOfDay();

        $reportType = $request->input('report_type', 'sales');

        //base query for summary counts
        $baseQuery = Order::with(['user', 'items'])
            ->whereBetween('createdAt', [$dateFrom, $dateTo]);

        $totalRevenue    = (clone $baseQuery)->whereNotIn('status', ['Canceled'])->sum('total');
        $totalOrders     = (clone $baseQuery)->count();
        $completedOrders = (clone $baseQuery)->where('status', 'Delivered')->count();
        $canceledOrders  = (clone $baseQuery)->where('status', 'Canceled')->count();

        if ($reportType === 'sales') {
            //sales summary: daily revenue breakdown
            $dailySales = Order::whereBetween('createdAt', [$dateFrom, $dateTo])
                ->whereNotIn('status', ['Canceled'])
                ->selectRaw('DATE(createdAt) as sale_date, COUNT(*) as order_count, SUM(total) as revenue')
                ->groupBy('sale_date')
                ->orderBy('sale_date', 'desc')
                ->paginate(15)
                ->withQueryString();

            //top customers by spend in range
            $topCustomers = Order::with('user')
                ->whereBetween('createdAt', [$dateFrom, $dateTo])
                ->whereNotIn('status', ['Canceled'])
                ->selectRaw('userID, COUNT(*) as order_count, SUM(total) as total_spent')
                ->groupBy('userID')
                ->orderByDesc('total_spent')
                ->take(5)
                ->get();

            return view('admin.reports', compact(
                'dailySales', 'topCustomers',
                'totalRevenue', 'totalOrders', 'completedOrders', 'canceledOrders',
                'reportType'
            ));
        }

        //orders: full order list with pagination
        $orders = (clone $baseQuery)->latest('createdAt')->paginate(10)->withQueryString();

        return view('admin.reports', compact(
            'orders',
            'totalRevenue', 'totalOrders', 'completedOrders', 'canceledOrders',
            'reportType'
        ));
    }

    public function reportsPdf(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from, 'Asia/Manila')->startOfDay()
            : now('Asia/Manila')->startOfMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to, 'Asia/Manila')->endOfDay()
            : now('Asia/Manila')->endOfDay();

        $reportType = $request->input('report_type', 'sales');
        $fromLabel  = $dateFrom->format('M d, Y');
        $toLabel    = $dateTo->format('M d, Y');

        $baseOrders = Order::with(['user', 'items'])
            ->whereBetween('createdAt', [$dateFrom, $dateTo]);

        $totalRevenue    = (clone $baseOrders)->whereNotIn('status', ['Canceled'])->sum('total');
        $totalOrders     = (clone $baseOrders)->count();
        $completedOrders = (clone $baseOrders)->where('status', 'Delivered')->count();
        $canceledOrders  = (clone $baseOrders)->where('status', 'Canceled')->count();

        if ($reportType === 'sales') {
            $dailySales = Order::whereBetween('createdAt', [$dateFrom, $dateTo])
                ->whereNotIn('status', ['Canceled'])
                ->selectRaw('DATE(createdAt) as sale_date, COUNT(*) as order_count, SUM(total) as revenue')
                ->groupBy('sale_date')
                ->orderBy('sale_date', 'desc')
                ->get();

            $topCustomers = Order::with('user')
                ->whereBetween('createdAt', [$dateFrom, $dateTo])
                ->whereNotIn('status', ['Canceled'])
                ->selectRaw('userID, COUNT(*) as order_count, SUM(total) as total_spent')
                ->groupBy('userID')
                ->orderByDesc('total_spent')
                ->take(5)
                ->get();

            $pdf = Pdf::loadView('admin.reports-pdf', compact(
                'dailySales', 'topCustomers',
                'totalRevenue', 'totalOrders', 'completedOrders', 'canceledOrders',
                'fromLabel', 'toLabel', 'reportType'
            ))->setPaper('a4', 'landscape');
        } else {
            $orders = (clone $baseOrders)->latest('createdAt')->get();

            $pdf = Pdf::loadView('admin.reports-pdf', compact(
                'orders',
                'totalRevenue', 'totalOrders', 'completedOrders', 'canceledOrders',
                'fromLabel', 'toLabel', 'reportType'
            ))->setPaper('a4', 'landscape');
        }

        $filename = 'mayari-' . $reportType . '-report-' . now('Asia/Manila')->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    private function orderAnalytics(): array
    {
        $counts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status')->toArray();
        foreach (Order::allStatuses() as $s) { $counts[$s] = $counts[$s] ?? 0; }
        return $counts;
    }

    private function normalizeVariants(?string $raw): ?array
    {
        if (!$raw) {
            return null;
        }

        $items = array_values(array_filter(array_map(
            fn ($v) => trim($v),
            explode(',', $raw)
        )));

        return empty($items) ? null : $items;
    }

    /**
     * Remove a previously uploaded file under public/; skip placeholder and remote URLs.
     */
    private function deleteLocalProductImageIfAny(?string $stored): void
    {
        if (!$stored || $stored === 'example.image') {
            return;
        }
        if (preg_match('#^https?://#i', $stored)) {
            return;
        }
        $path = public_path($stored);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}