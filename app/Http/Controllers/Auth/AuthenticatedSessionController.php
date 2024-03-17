<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    
        $user = User::where('email', $request->email)->firstOrFail();
        $id = $user->id;
        $token = $user->createToken('api-token')->plainTextToken;
    
        return response()->json(['token' => $token, 'id' => $id], 200);
    }

    public function destroy(Request $request){
        $user = $request->user();
        
        if ($user) {
            $request->user()->currentAccessToken()->delete();
            $request->user()->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
