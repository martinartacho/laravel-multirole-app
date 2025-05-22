<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator; // Importación crítica
use Illuminate\Support\Facades\Log; // Para logging

class AuthController extends Controller
{
    public function __construct()
    {
        // Exceptuar el login del middleware auth:api
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Login y generación de token JWT.
     */
    
    public function login(Request $request)
    {
        // Validación manual con mensajes claros
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Debe ser un email válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        if ($validator->fails()) {
            Log::warning('Error validación login', ['errors' => $validator->errors()]); // Log
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Intento de autenticación
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Intento fallido de login', ['email' => $request->email]); // Log
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Generar token (Sanctum)
        try {
            $token = Auth::user()->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => Auth::user()->only('id', 'name', 'email') // Datos básicos del usuario
            ]);

        } catch (\Exception $e) {
            Log::error('Error generando token', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno al generar token'
            ], 500);
        }
    }

    /**
     * Obtener usuario autenticado.
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Cerrar sesión (invalidate token).
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * Responder con token.
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
