<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function __construct()
    {
        if (!Session::has('cart')) {
            Session::put('cart', []);
        }
    }

    public function items()
    {
        $cart = Session::get('cart', []);
        $items = [];
        
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = [
                    'id' => $product->id,
                    'nombre' => $product->name, // Asegúrate de que el campo se llama 'name' en tu BD
                    'precio' => $product->precio, // Asegúrate de que el campo se llama 'precio' en tu BD
                    'cantidad' => $quantity,
                    'imagen' => $product->image_url // Ajusta según tu estructura de BD
                ];
            }
        }
        
        return $items;
    }

    public function add($productId)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]++;
        } else {
            $cart[$productId] = 1;
        }
        
        Session::put('cart', $cart);
    }

    public function remove($productId)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
        }
    }

    public function updateQuantity($productId, $change)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $change;
            
            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }
            
            Session::put('cart', $cart);
        }
    }

    public function clear()
    {
        Session::forget('cart');
    }

    public function total()
    {
        $items = $this->items();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        return $total;
    }

    public function count()
    {
        $cart = Session::get('cart', []);
        return array_sum($cart);
    }

    public function validateStock()
    {
        $items = $this->items();
        $errors = [];
        
        foreach ($items as $item) {
            $product = Product::find($item['id']);
            if ($product && $product->stock < $item['cantidad']) {
                $errors[] = "No hay suficiente stock para {$product->name}. Stock disponible: {$product->stock}";
            }
        }
        
        return $errors;
    }
}