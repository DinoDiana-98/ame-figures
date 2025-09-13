<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem; // ← Añadir esta importación
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics.
     */
    public function index()
    {
        // Estadísticas principales
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'active_products' => Product::where('activo', true)->count(),
            'inactive_products' => Product::where('activo', false)->count(),
            'low_stock_products' => Product::where('stock', '<=', DB::raw('stock_minimo'))->count(),
            'out_of_stock_products' => Product::where('stock', 0)->count(),
        ];

        // Productos con stock bajo
        $lowStockProducts = Product::where('stock', '<=', DB::raw('stock_minimo'))
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Últimos productos agregados
        $recentProducts = Product::with('category')
            ->latest()
            ->limit(5)
            ->get();

        // Últimas órdenes
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Categorías con más productos
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        // Productos más vendidos - CORREGIDO: cantidad → quantity
        $bestSellingProducts = Product::withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)')); // ← Cambiado a quantity
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'lowStockProducts',
            'recentProducts',
            'recentOrders',
            'topCategories',
            'bestSellingProducts'
        ));
    }

    /**
     * Get sales data for charts (puedes usar esto para gráficos)
     */
    public function getSalesData(Request $request)
    {
        $days = $request->get('days', 30);
        
        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total) as total_revenue')
        )
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($salesData);
    }

    /**
     * Quick actions from dashboard
     */
    public function quickAction(Request $request)
    {
        $action = $request->action;

        switch ($action) {
            case 'add_product':
                return redirect()->route('admin.products.create');
                
            case 'add_category':
                return redirect()->route('admin.categories.create');
                
            case 'view_orders':
                return redirect()->route('admin.orders.index');
                
            case 'low_stock':
                return redirect()->route('admin.products.index', ['filter' => 'low_stock']);
                
            default:
                return redirect()->back()->with('error', 'Acción no válida');
        }
    }
}