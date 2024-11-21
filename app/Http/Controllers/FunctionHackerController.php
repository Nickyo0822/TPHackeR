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
     *     path="/email/exist",
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="L'email n'existe pas",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="L'email n'existe pas")
     *         )
     *     ),
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
}
