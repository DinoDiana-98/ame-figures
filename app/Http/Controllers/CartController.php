<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $stockErrors = $this->cartService->validateStock();
        $items = $this->cartService->items();
        
        if(empty($items)) {
            return view('cart.index', [
                'items' => [],
                'stockErrors' => $stockErrors,
                'subtotal' => 0,
                'total' => 0,
                'cartCount' => 0
            ]);
        }
        
        $subtotal = $this->cartService->total();
        $total = $subtotal; // Envío se calculará después según la ciudad
        
        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $total,
            'cartCount' => $this->cartService->count(),
            'stockErrors' => $stockErrors
        ]);
    }

    public function add($id)
    {
        try {
            $this->cartService->add($id);
            return redirect()->back()->with('success', 'Producto añadido al carrito');
        } catch (\Exception $e) {
            Log::error('Error adding product to cart: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove($id)
    {
        try {
            $this->cartService->remove($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error removing product from cart: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el producto']);
        }
    }

    public function clear()
    {
        try {
            $this->cartService->clear();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al vaciar el carrito']);
        }
    }

    public function increment($id)
    {
        try {
            $this->cartService->updateQuantity($id, 1);
            return redirect()->back()->with('success', 'Cantidad actualizada');
        } catch (\Exception $e) {
            Log::error('Error incrementing quantity: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function decrement($id)
    {
        try {
            $this->cartService->updateQuantity($id, -1);
            return redirect()->back()->with('success', 'Cantidad actualizada');
        } catch (\Exception $e) {
            Log::error('Error decrementing quantity: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function calculateShipping(Request $request)
    {
        try {
            $city = strtolower(trim($request->city));
            $isTrujillo = $city === 'trujillo';
            $shipping = $isTrujillo ? 0 : 11;
            $subtotal = $this->cartService->total();
            $total = $subtotal + $shipping;

            return response()->json([
                'success' => true,
                'shipping' => $shipping,
                'total' => $total,
                'isTrujillo' => $isTrujillo
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating shipping: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular envío'
            ], 500);
        }
    }

    public function checkoutWhatsApp(Request $request)
    {
        try {
            $request->validate([
                'department' => 'required|string|max:80',
                'city' => 'required|string|max:80',
                'district' => 'required|string|max:120',
            ]);

            $items = $this->cartService->items();
            if (empty($items)) {
                return back()->with('error', 'Tu carrito está vacío.');
            }

            $subtotal = $this->cartService->total();
            $isTrujillo = strtolower(trim($request->city)) === 'trujillo';
            $requiresShipping = !$isTrujillo;

            // Construir mensaje de WhatsApp
            $message = $this->buildWhatsAppMessage($items, $subtotal, $requiresShipping, $request);

            $encodedMessage = urlencode($message);
            $whatsappNumber = config('app.whatsapp_number', '51922511532');
            $whatsappLink = "https://wa.me/{$whatsappNumber}?text={$encodedMessage}";

            // Limpiar carrito después del checkout exitoso
            $this->cartService->clear();

            return redirect()->away($whatsappLink);

        } catch (\Exception $e) {
            Log::error('Error en checkout: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el pedido. Por favor, inténtelo de nuevo.');
        }
    }

    private function buildWhatsAppMessage($items, $subtotal, $shipping, $total, $requiresShipping, $request)
    {
        $message = "🛒 *NUEVO PEDIDO* 🛒\n\n";
        $message .= "Hola, quiero comprar:\n";
        
        foreach ($items as $item) {
            $message .= "• {$item['nombre']} x{$item['cantidad']} = S/ " . number_format($item['precio'] * $item['cantidad'], 2) . "\n";
        }
        
        $message .= "\n📊 *Subtotal:* S/ " . number_format($subtotal, 2) . "\n";
        
        if ($requiresShipping) {
            $message .= "🚚 *Costo de envío:* S/ " . number_format($shipping, 2) . "\n";
            $message .= "📦 *¡Envío fuera de Trujillo!*\n\n";
        } else {
            $message .= "🚚 *Costo de envío:* S/ " . number_format($shipping, 2) . " (Gratis - Trujillo)\n\n";
        }
        
        $message .= "💰 *TOTAL:* S/ " . number_format($total, 2) . "\n\n";
        
        $message .= "📍 *Ubicación:* " . ucfirst(strtolower($request->department)) . " - " . ucfirst(strtolower($request->city));
        $message .= " / " . ucfirst(strtolower($request->district));
        
        $message .= "\n\n";
        
        if ($requiresShipping) {
            $message .= "🌍 *ENVÍO FUERA DE TRUJILLO:*\n";
            $message .= "• Coordinaremos el envío mediante WhatsApp\n";
            $message .= "• Tiempo de entrega: 2-3 días hábiles\n\n";
        } else {
            $message .= "📍 *ENTREGA EN TRUJILLO:*\n";
            $message .= "• Coordinamos punto de entrega\n";
            $message .= "• Tiempo de entrega: 24 horas\n\n";
        }
        
        $message .= "Por favor confirmar disponibilidad y coordinar entrega ✅";

        return $message;
    }
}