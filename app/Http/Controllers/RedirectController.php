<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function home()
    {
        // Siempre redirigir al catálogo
        return redirect()->route('products.index');
    }

    public function afterLogin()
    {
        // Solo para admin
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        
        // En caso de que un usuario no admin intente acceder
        return redirect()->route('products.index');
    }

    public function afterLogout()
    {
        return redirect()->route('products.index');
    }
}