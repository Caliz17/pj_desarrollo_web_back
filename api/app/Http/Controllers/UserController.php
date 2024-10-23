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
      * @OA\Post(
      *     path="/api/register",
      *     summary="Registra un nuevo usuario",
      *     tags={"Usuarios"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             @OA\Property(property="name", type="string", example="John Doe"),
      *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
      *             @OA\Property(property="password", type="string", example="password123"),
      *             @OA\Property(property="password_confirmation", type="string", example="password123")
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Usuario registrado correctamente",
      *         @OA\JsonContent(
      *             @OA\Property(property="status", type="integer", example=200),
      *             @OA\Property(property="success", type="boolean", example=true),
      *             @OA\Property(property="message", type="string", example="Usuario registrado correctamente")
      *         )
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Error de validación",
      *         @OA\JsonContent(
      *             @OA\Property(property="status", type="integer", example=400),
      *             @OA\Property(property="success", type="boolean", example=false),
      *             @OA\Property(property="message", type="string", example="Error al registrar usuario")
      *         )
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Error interno del servidor",
      *         @OA\JsonContent(
      *             @OA\Property(property="status", type="integer", example=500),
      *             @OA\Property(property="success", type="boolean", example=false),
      *             @OA\Property(property="message", type="string", example="Error al registrar usuario")
      *         )
      *     )
      * )
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
     * @OA\Post(
     *     path="/api/login",
     *     summary="Inicia sesión de un usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al iniciar sesión"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El campo email es obligatorio.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="El campo password es obligatorio."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=401),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Credenciales inválidas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se pudo crear el token")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/login/response",
     *     summary="Responder con el token",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión correcto",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Inicio de sesión correcto"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600),
     *             @OA\Property(property="name", type="string", example="JOHN DOE")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/user/profile",
     *     summary="Obtener el perfil de usuario autenticado",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Perfil de usuario recuperado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Perfil de usuario"),
     *                    )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=401),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/login/google",
     *     summary="Iniciar sesión con Google",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"google_id", "google_token", "email", "name"},
     *             @OA\Property(property="google_id", type="string", example="1234567890"),
     *             @OA\Property(property="google_token", type="string", example="ya29.a0AfH6SMA..."),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="name", type="string", example="John Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="name", type="string", example="JOHN DOE")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={
     *                 "google_id": {"El campo google id es obligatorio."},
     *                 "google_token": {"El campo google token es obligatorio."},
     *                 "email": {"El campo email debe ser un correo electrónico válido."},
     *                 "name": {"El campo name es obligatorio."}
     *             })
     *         )
     *     )
     * )
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
