<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CartService;

class ProductController extends Controller
{
    /**
     * Mostrar catálogo de productos con filtros
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('activo', true)
            ->where('stock', '>', 0); // Solo productos con stock
        
        // Filtro por categoría
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // Filtro por búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('descripcion', 'like', '%' . $search . '%');
            });
        }

        $products = $query->orderBy('destacado', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(12);
        
        $categories = Category::where('activo', true)->get();

        return view('catalogo', compact('products', 'categories'));
    }

    /**
     * Mostrar detalle de producto
     */
    public function show($slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('activo', true)
            ->firstOrFail();

        // Productos relacionados (solo con stock)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('catalogo-show', compact('product', 'relatedProducts'));


        
    }
    
    /**
     * Comprar producto directamente
     */
    public function buyNow($id, CartService $cart)
    {
        try {
            $product = Product::where('id', $id)
                ->where('activo', true)
                ->where('stock', '>', 0)
                ->firstOrFail();

            // Limpiar carrito y agregar solo este producto
            $cart->clear();
            $cart->add($product->id, 1);

            return redirect()->route('cart.index')
                ->with('success', 'Producto agregado. Continúa con tu compra.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Producto no disponible');
        }
    }

    public function catalogo(Request $request)
{
    $query = Product::with('category')
                    ->where('activo', true)
                    ->latest();
    
    // Filtros y búsqueda
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('descripcion', 'like', "%{$search}%")
                ->orWhereHas('category', function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
            });
        });
    }
    
    // Filtrar por categoría
    if ($request->has('categoria')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->categoria);
        });
    }
    
    $products = $query->paginate(12);
    $categories = Category::where('activo', true)->get();
    
    return view('catalogo', compact('products', 'categories'));
}


public function byCategory($categorySlug)
{
    $category = Category::where('slug', $categorySlug)
                        ->where('activo', true)
                        ->firstOrFail();
        
    $products = Product::where('category_id', $category->id)
                        ->where('activo', true)
                        ->paginate(12);
    
    return view('catalogo', compact('products', 'category'));
}

}