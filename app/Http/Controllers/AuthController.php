<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Hash;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();

        return Hash::make( $input['password'] );

        $validated = $request->validated();

        if (!$validated) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah',
            ], 401);
        }

        $input['password'] = Hash::make($request->password);

        $user = User::create($input);

        $token = $user->createToken('User Token')->accessToken;

        return response()->json(
            [ 
                'success' => true,
                'message' => 'Berhasil mendaftarkan pengguna',
                'data' => [
                    'user' => $user, 
                    'token' => $token
                ]
            ]
        );
    }

    public function login(LoginRequest $request)
    {
        $input = $request->all();
        $validated = $request->validated();

        if (!$validated) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah',
            ], 401);
        }
        
        $user = User::where('username', $input['username'])->first();
        if (!$user || !Hash::check($input['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }
        if ($user->is_suspended) {
            return response()->json([
                'success' => false,
                'message' => 'Akun anda terblokir',
                'data' => [
                    'user' => $user
                ]
            ], 401);
        }

        $token = $user->createToken('User Token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Berhasil login',
            'data' => [
                'user' => $user->load('UserBank'),
                'token' => $token
            ]
        ]);
    }

    public function getProfile() {
        return response()->json([
            'success' => true,
            'message' => "Berhasil mendapatkan data user",
            "data" => Auth::user()->load('UserBank')
        ], 200);
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
