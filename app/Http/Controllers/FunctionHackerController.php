<?php

namespace App\Http\Controllers;

use App\Models\CommonUtilitary;
use App\Models\Logs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FunctionHackerController extends Controller
{
    /**
     * @OA\Post(
     *     path="auth/email-exist",
     *     summary="Vérifie si l'email existe",
     *     description="Vérifie si l'email existe",
     *     tags={"email"},
     * @OA\Parameter(
     *         name="bearerToken",
     *         in="query",
     *         description="Token de connexion de l'utilisateur",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTg1Ljk4LjEzOC41Ni9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTczMDM4OTU1NSwiZXhwIjoxNzMwMzkzMTU1LCJuYmYiOjE3MzAzODk1NTUsImp0aSI6IjNUYmNnWHIxMjNDaGVpVkQiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.do2Iv4GOl28ppLio4W3u1BF30_WoBA5E9nEae7ULwGQ"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="L'email est valide",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com")
     *         ),
     *     )
     * )
     */
    public function EmailVerificator()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("EmailVerificator");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "EmailVerificator");

        $email = request()->email;

        $response = Http::get(CommonUtilitary::API_HUNTER_IO."email-verifier", [
            'email' => $email,
            'api_key' => CommonUtilitary::API_KEY_HUNTER_IO
        ]);

        return response()->json($response->json());
    }

    /**
     * @OA\Post(
     *     path="auth/common-password",
     *     summary="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     description="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     tags={"password"},
     * @OA\Parameter(
     *         name="bearerToken",
     *         in="query",
     *         description="Token de connexion de l'utilisateur",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTg1Ljk4LjEzOC41Ni9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTczMDM4OTU1NSwiZXhwIjoxNzMwMzkzMTU1LCJuYmYiOjE3MzAzODk1NTUsImp0aSI6IjNUYmNnWHIxMjNDaGVpVkQiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.do2Iv4GOl28ppLio4W3u1BF30_WoBA5E9nEae7ULwGQ"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le mot de passe est utilisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="string", type="string", format="string", example="test")
     *         ),
     *     )
     * )
     */
    public function getCommonPasswords()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("getCommonPasswords");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "getCommonPasswords");

        $url = 'https://raw.githubusercontent.com/danielmiessler/SecLists/master/Passwords/Common-Credentials/10k-most-common.txt';
        $response = Http::get($url);

        $content = $response->body();
        $passwords = explode("\n", $content);

        $checkPassword = request()->password;

        if (in_array($checkPassword, $passwords))
        {
            return response()->json(['message' => 'Le mot de passe fait partie des mots de passe les plus fréquents'], 200);
        }
        else 
        {
            return response()->json(['message' => 'Le mot de passe ne fait pas partie des mots de passe les plus fréquents'], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="auth/get-all-domains",
     *     summary="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     description="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     tags={"password"},
     * @OA\Parameter(
     *         name="bearerToken",
     *         in="query",
     *         description="Token de connexion de l'utilisateur",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTg1Ljk4LjEzOC41Ni9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTczMDM4OTU1NSwiZXhwIjoxNzMwMzkzMTU1LCJuYmYiOjE3MzAzODk1NTUsImp0aSI6IjNUYmNnWHIxMjNDaGVpVkQiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.do2Iv4GOl28ppLio4W3u1BF30_WoBA5E9nEae7ULwGQ"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le mot de passe est utilisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="string", type="string", format="string", example="test")
     *         ),
     *     )
     * )
     */
    public function getAllDomains()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://api.securitytrails.com/v1/ping', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);


        return $response()->json();
    }
}
