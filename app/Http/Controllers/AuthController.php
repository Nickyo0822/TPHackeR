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
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @SWG\Get(
     *     path="/login",
     *     summary="Login user",
     *     tags={"users"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
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
     * @SWG\Get(
     *     path="/me",
     *     summary="Get informations about current user",
     *     tags={"users"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
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
     * @SWG\Get(
     *     path="/logout",
     *     summary="Logout user",
     *     tags={"users"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
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
     * @SWG\Get(
     *     path="/refresh",
     *     summary="Refresh current token user",
     *     tags={"users"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
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
     * Get the token array structure.
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
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/register",
     *     summary="Register an user",
     *     tags={"users"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
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