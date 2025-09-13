<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;

class AdminProductController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // ✅ AGREGAR Request $request
    {
        $query = Product::with('category')->latest();
        $categories = Category::all();

        // Búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('descripcion', 'like', "%{$search}%")
                ->orWhereHas('category', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
                });
            });
        }

        $products = $query->paginate(10);
        return view('admin.products.index', compact('products', 'categories'));
    }

    // Formulario crear producto
    public function create()
    {
        $categories = Category::where('activo', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    // Guardar producto
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku|max:50',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'material' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'incluye' => 'nullable|string|max:255',
            'imagen1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagen2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagen3' => 'nullable|image|mimes:jpeg,png,jjpg,gif,webp|max:2048',
            'precio_descuento' => 'nullable|numeric|min:0', // ✅ AGREGADO
            'stock_minimo' => 'required|integer|min:0', // ✅ AGREGADO
            'activo' => 'boolean',
            'destacado' => 'boolean' // ✅ AGREGADO
        ]);

        // ✅ GENERAR SLUG AUTOMÁTICAMENTE (desde el nombre)
        $validated['slug'] = Str::slug($validated['nombre']);

        // ✅ CAMPOS POR DEFECTO
        $validated['activo'] = $request->has('activo') ?? true;
        $validated['destacado'] = $request->has('destacado') ?? false;
        $validated['stock_minimo'] = $request->stock_minimo ?? 5;

        // Manejar imágenes
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "imagen{$i}";
            if ($request->hasFile($fieldName)) {
                $validated[$fieldName] = $request->file($fieldName)->store('products', 'public');
            } else {
                $validated[$fieldName] = null;
            }
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    // Mostrar producto
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    // Formulario editar producto
    public function edit(Product $product)
    {
        $categories = Category::where('activo', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Actualizar producto
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'material' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'incluye' => 'nullable|string|max:255',
            'imagen1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagen2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagen3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'precio_descuento' => 'nullable|numeric|min:0', // ✅ AGREGADO
            'stock_minimo' => 'required|integer|min:0', // ✅ AGREGADO
            'activo' => 'boolean',
            'destacado' => 'boolean' // ✅ AGREGADO
        ]);

        // ✅ ACTUALIZAR SLUG SOLO SI CAMBIÓ EL NOMBRE
        if ($product->nombre != $validated['nombre']) {
            $validated['slug'] = Str::slug($validated['nombre']);
        }

        // ✅ ACTUALIZAR CAMPOS
        $validated['activo'] = $request->has('activo') ?? true;
        $validated['destacado'] = $request->has('destacado') ?? false;
        $validated['stock_minimo'] = $request->stock_minimo ?? 5;

        // Manejar imágenes (mantener las existentes si no se suben nuevas)
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "imagen{$i}";
            if ($request->hasFile($fieldName)) {
                // Eliminar imagen anterior si existe
                if ($product->$fieldName) {
                    Storage::disk('public')->delete($product->$fieldName);
                }
                $validated[$fieldName] = $request->file($fieldName)->store('products', 'public');
            } else {
                // Mantener la imagen existente
                $validated[$fieldName] = $product->$fieldName;
            }
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    // Eliminar producto
    public function destroy(Product $product)
    {
        // Eliminar imágenes
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "imagen{$i}";
            if ($product->$fieldName) {
                Storage::disk('public')->delete($product->$fieldName);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}