<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->symbols()]
        ]);

        if ($validator->fails()) :
            return response([
                'error' => $validator->messages()
            ], 422);
        endif;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->all();

        $validator = Validator::make($credentials, [
            'email' => 'required|email|string|exists:users,email',
            'password' => 'required',
            'remember' => 'boolean'
        ]);


        if ($validator->fails()) :
            return response([
                'error' => $validator->messages()
            ], 422);
        endif;

        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember)) :
            return response([
                'error' => ["credentials" => ["The Provided Credentials are not correct"]]
            ], 422);
        endif;

        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response([
            'success' => true,
        ]);
    }
}
