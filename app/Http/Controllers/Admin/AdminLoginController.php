<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Mostrar formulario de login para admin
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Procesar el login del admin
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Intentar autenticar como admin (is_admin = true)
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_admin' => true])) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', '¡Bienvenido al panel de administración!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con un usuario administrador.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión del admin
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}