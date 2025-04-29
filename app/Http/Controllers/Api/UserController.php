<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   // Register
   public function register(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'name'     => 'required|string|max:255',
           'email'    => 'required|string|email|max:255|unique:users',
           'password' => 'required|string|min:6|confirmed',
       ]);

       if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
       }

       $user = User::create([
           'name'     => $request->name,
           'email'    => $request->email,
           'password' => Hash::make($request->password),
       ]);

       $token = $user->createToken('authToken')->accessToken;

       return response()->json(['token' => $token, 'user' => $user], 201);
   }

   // Login
   public function login(Request $request)
   {
       $credentials = $request->only('email', 'password');

       if (!Auth::attempt($credentials)) {
           return response()->json(['message' => 'Invalid login credentials'], 401);
       }

       $user  = Auth::user();
       $token = $user->createToken('authToken')->accessToken;

       return response()->json(['token' => $token, 'user' => $user], 200);
   }

   // Logout
   public function logout(Request $request)
   {
       $request->user()->token()->revoke();
       return response()->json(['message' => 'Logged out successfully'], 200);
   }
}
