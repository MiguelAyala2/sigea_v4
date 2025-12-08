<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Login\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Retornar la vista de autenticación.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Autenticar al usuario en la aplicación.
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'activo' => true])) {
            User::registrarAcceso(User::where('usuario', $request->usuario)->value('id'));
            return redirect()->route('home');
        }

        // Si llegamos aquí, la autenticación falló
        return back()->withErrors(['usuario' => 'Credenciales inválidas']);
    }

    /**
     * Cerrar la sesión del usuario de la aplicación.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
