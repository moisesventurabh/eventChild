<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'As credenciais fornecidas estão incorretas.'
            ], 422);
        }

        $user = $request->user();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Autenticado com sucesso.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }


    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso e token revogado.'
        ], 200);
    }
}