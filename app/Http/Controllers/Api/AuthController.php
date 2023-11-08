<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use PDO;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors(),
                'error' => 'Unprocessed Entity'
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Invalid Credentials!',
                    'error' => 'Unauthorized'
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Please contact administrator!',
                'error' => 'Internal Server Error'
            ], 500);
        }


        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|between:2,100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        if ($user) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'User successfully registered',
                'user'    => $user,
            ], 201);
        }

        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => 'User register failed',
            'user' => ''
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            return $this->createNewToken(auth()->refresh());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'success' => true,
            'status' => 200,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->guard('api')->user()
        ], 200);
    }

    public function checkAuth(Request $request)
    {
        $user = auth()->guard('api')->user();
        if ($user) {
            return response()->json(['authenticated' => true, 'user' => $user]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    }
}
