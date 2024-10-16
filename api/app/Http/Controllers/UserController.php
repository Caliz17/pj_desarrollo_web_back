<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userRegister(Request $request)
    {
        $status = null;
        $message = null;
        $success = null;
        $request = $request->all();

        // reglas
        $validator = Validator::make($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator) {
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $result = $user->save();
            if ($result) {
                $status = Response::HTTP_OK;
                $message = 'Usuario registrado correctamente';
                $success = true;
            } else {
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
                $message = 'Error al registrar usuario';
                $success = false;
                Log::error('Error al registrar usuario'. $validator->errors());
            }
        } else {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $message = 'Error al registrar usuario';
            $success = false;
            Log::error('Error al registrar usuario'. $validator->errors());
        }

        return response()->json([
            'status' => $status,
            'success' => $success,
            'message' => $message,
        ]);
    }

    /**
     * Login a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userLogin(Request $request)
    {
        $data = $request->only('email', 'password');

        // Validación
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Error al iniciar sesión',
                'errors' => $validator->errors(),
            ]);
        }

        // Intentar autenticación con JWT
        try {
            if (!$token = JWTAuth::attempt($data)) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Credenciales inválidas',
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'No se pudo crear el token',
            ]);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Responder con el token
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithToken($token)
    {
        return response()->json([
            'status' => Response::HTTP_OK,
            'success' => true,
            'message' => 'Inicio de sesión correcto',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'name' => strtoupper(auth()->user()->name),
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        if (auth()->user()) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Perfil de usuario',
                'data' => auth()->user(),
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'success' => false,
                'message' => 'No autorizado',
            ]);
        }
    }

    /**
     * Login with Google
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'google_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $user->update([
                'google_id' => $data['google_id'],
                'google_token' => $data['google_token'],
            ]);
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'google_id' => $data['google_id'],
                'google_token' => $data['google_token'],
            ]);
        }

        return response()->json([
            'token' => JWTAuth::fromUser($user),
            'message' => 'Login successful',
            'name' => strtoupper($user->name),
        ]);
    }
}
