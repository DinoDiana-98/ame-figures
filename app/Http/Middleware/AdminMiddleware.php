<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que esté autenticado y sea admin
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Por favor inicia sesión primero.');
        }

        if (!Auth::user()->is_admin) {
            return redirect()->route('products.index')
                ->with('error', 'Acceso no autorizado. Solo administradores pueden acceder.');
        }

        return $next($request);
    }
}