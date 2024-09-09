<?php

namespace App\Http\Controllers\Api\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\LoginRequest;
use App\Http\Requests\Auth\V1\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register'
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        if($user){
            $token = Auth::login($user);
            return $this->responseWithToken($token, $user);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occure while trying to create user',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $token = Auth::attempt($request->validated());
        if($token){
            return $this->responseWithToken($token, Auth::user());
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }
    }


    public function refresh()
    {
        try {
            $newToken = Auth::refresh();
            return $this->responseWithToken($newToken, Auth::user());
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Token has expired',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not refresh the token',
            ], 500);
        }
    }


    public function current()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'User has been logged out',
        ]);
    }

    protected function responseWithToken($token, $user)
    {
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'token_type' => 'bearer',
        ]);
    }
}
