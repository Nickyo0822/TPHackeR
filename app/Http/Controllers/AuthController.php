<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\ListMessagesError;
use App\Models\Logs;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\OpenApi(
     *     @OA\Info(
     *         title="TPHackerR",
     *         version="0.0.1",
     *         description="Une API pour s'amuser"
     *     )
     * )
     *
     * @OA\Server(
     *     url="http://localhost/api",
     *     description="Serveur local"
     * )
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login user",
     *     description="Use the login function to connect user",
     *     tags={"users"},
     * @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Adresse email de l'utilisateur",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="email",
     *             example="user@example.com"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Mot de passe de l'utilisateur",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="password",
     *             example="password123"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie, token de l'utilisateur retourné",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec de la connexion",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Pas le bon mot de passe")
     *         )
     *     ),
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            Logs::create([
                'usedFonction' => 'login',
                'content' => 'Tentative de connexion avec l\'email : ' . $credentials['email'] . ' | mot de passe : ' . $credentials['password']
            ]);

            return response()->json(['erreur' => 'Pas le bon mot de passe'], 401);
        }

        Logs::create([
            'usedFonction' => 'login',
            'content' => 'L\'utilisateur ' . User::find(Auth::id())['name'] . ' s\'est connecté',
            'user_id' => Auth::id()
        ]);

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/auth/me",
     *     summary="Post informations about current user",
     *     tags={"users"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function me()
    {
        if (!Auth::id())
        {
            Logs::create([
                'usedFonction' => 'me',
                'content' => ListMessagesError::USER_NOT_CONNECTED
            ]);

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        Logs::create([
            'usedFonction' => 'me',
            'content' => 'L\'utilisateur ' . User::find(Auth::id())['name'] . ' a utilisé la fonction "me"',
            'user_id' => Auth::id()
        ]);

        return response()->json(Auth::user());
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout user",
     *     tags={"users"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function logout()
    {
        Logs::create([
            'usedFonction' => 'logout',
            'content' => 'L\'utilisateur ' . User::find(Auth::id())['name'] . ' s\'est déconnecté',
            'user_id' => Auth::id()
        ]);

        Auth::logout();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Refresh current token user",
     *     tags={"users"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function refresh()
    {
        Logs::create([
            'usedFonction' => 'refresh',
            'content' => 'L\'utilisateur ' . User::find(Auth::id())['name'] . ' a refresh son token',
            'user_id' => Auth::id()
        ]);

        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Post the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => (Auth::factory()->getTTL() ?: config('jwt.ttl')) * 60
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register an user",
     *     tags={"users"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function register() 
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'profiles_id' => 'required'
        ]);

        if ($validator->fails()){
            Logs::create([
                'usedFonction' => 'register',
                'content' => $validator->errors(),
                'created_at' => now()
            ]);
            
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        $user = new User();
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = bcrypt(request()->password);
        $user->profiles_id = request()->profiles_id;
        $user->save();
        
        Logs::create([
            'content' => 'Création de l\'utilisateur ' . request()->name,
            'created_at' => now()
        ]);
        
        return response()->json($user, 201);
    }
}