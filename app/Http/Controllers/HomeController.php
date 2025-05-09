<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Today's metrics
        $todaySales = Transaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('grand_total');

        $todayTransactions = Transaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        // Monthly metrics
        $monthlySales = Transaction::whereMonth('created_at', now()->month)
            ->where('status', 'completed')
            ->sum('grand_total');

        $monthlyTransactions = Transaction::whereMonth('created_at', now()->month)
            ->where('status', 'completed')
            ->count();

        // Inventory metrics
        $lowStockCount = Inventory::whereColumn('quantity', '<=', 'reorder_level')
            ->count();

        // Top product - Fixed query
        $topProduct = Product::select([
                'products.id',
                'products.name',
                DB::raw('SUM(transaction_items.subtotal) as total_sales')
            ])
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales')
            ->first();

        $topProductSales = $topProduct->total_sales ?? 0;

        // Top cashier - Fixed query
        $topCashier = User::select([
                'users.id',
                'users.name',
                DB::raw('SUM(transactions.grand_total) as total_sales')
            ])
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->first();

        $topCashierSales = $topCashier->total_sales ?? 0;

        // Top outlet - Fixed query
        $topOutlet = Outlet::select([
                'outlets.id',
                'outlets.name',
                DB::raw('SUM(transactions.grand_total) as total_sales')
            ])
            ->join('transactions', 'outlets.id', '=', 'transactions.outlet_id')
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->groupBy('outlets.id', 'outlets.name')
            ->orderByDesc('total_sales')
            ->first();

        $topOutletSales = $topOutlet->total_sales ?? 0;

        // Sales trend (last 7 days)
        $salesTrend = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesTrendLabels = [];
        $salesTrendData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesTrendLabels[] = now()->subDays($i)->format('D, M j');
            $salesTrendData[] = $salesTrend->firstWhere('date', $date)->total ?? 0;
        }

        // Payment methods
        $paymentMethods = Transaction::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(grand_total) as total')
            )
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->groupBy('payment_method')
            ->get();

        $paymentMethodsLabels = $paymentMethods->pluck('payment_method')->map(function($method) {
            return ucfirst(str_replace('_', ' ', $method));
        })->toArray();

        $paymentMethodsData = $paymentMethods->pluck('total')->toArray();

        // Recent transactions
        $recentTransactions = Transaction::with(['user', 'outlet'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get full product/cashier/outlet objects if needed
        $topProductModel = $topProduct ? Product::find($topProduct->id) : null;
        $topCashierModel = $topCashier ? User::find($topCashier->id) : null;
        $topOutletModel = $topOutlet ? Outlet::find($topOutlet->id) : null;

        return view('home', compact(
            'todaySales',
            'todayTransactions',
            'monthlySales',
            'monthlyTransactions',
            'lowStockCount',
            'topProductModel',
            'topProductSales',
            'topCashierModel',
            'topCashierSales',
            'topOutletModel',
            'topOutletSales',
            'salesTrendLabels',
            'salesTrendData',
            'paymentMethodsLabels',
            'paymentMethodsData',
            'recentTransactions'
        ));
    }
}
