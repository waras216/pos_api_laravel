<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = Usuario::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message' => 'credenciales incorrectas'
            ],400);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
    
    public function register(Request $request){
        $data = $request -> validate([
            'email' => 'required|email|unique:usuario,email',
            'password' => 'required|min:6',
        ]);

        $user = Usuario::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ],201);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Sesion cerrada'
        ]);
    }
}
