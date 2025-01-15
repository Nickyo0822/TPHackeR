<?php

namespace App\Http\Controllers;

use App\Models\CommonUtilitary;
use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
     *     tags={"domains"},
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
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("getAllDomains");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "getAllDomains");

        $client = new \GuzzleHttp\Client();

        $domain = request()->domain;

        $response = $client->request('GET', CommonUtilitary::API_SECURITY_TRAILS . $domain . '/subdomains', [
            'headers' => [
              'APIKEY' => CommonUtilitary::API_KEY_SECURITY_TRAILS,
              'accept' => 'application/json',
            ],
        ]);

        return $response->getBody();
    }

    /**
     * @OA\Post(
     *     path="auth/password-generator",
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
    public function passwordGenerator()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("passwordGenerator");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "passwordGenerator");

        $client = new \GuzzleHttp\Client();

        $include_digits = "";
        $include_lowercase = "";
        $include_uppercase = "";
        $include_special_characters = "";
        $add_custom_characters = "";
        $exclude_similar_characters = "";
        $password_length = "password_length=12&";
        $quantity = "quantity=1";

        if (request()->include_digits != "")
        {
            $include_digits = "include_digits&";
        }
        if (request()->include_lowercase != "")
        {
            $include_lowercase = "include_lowercase&";
        }
        if (request()->include_uppercase != "")
        {
            $include_uppercase = "include_uppercase&";
        }
        if (request()->include_special_characters != "")
        {
            $include_special_characters = "include_special_characters&";
        }
        if (request()->add_custom_characters != "")
        {
            $add_custom_characters = 'add_custom_characters=(' . request()->add_custom_characters . ')&';
        }
        if (request()->exclude_similar_characters != "")
        {
            $exclude_similar_characters = "exclude_similar_characters&";
        }
        if (request()->password_length != "")
        {
            $password_length = 'password_length=' . request()->password_length . '&';
        }
        if (request()->quantity != "")
        {
            $quantity = 'quantity=' . request()->quantity;
        }

        $response = $client->request('GET', 'https://api.motdepasse.xyz/create/?' . $include_digits . $include_lowercase . $include_uppercase . $include_special_characters . $add_custom_characters . $exclude_similar_characters . $password_length . $quantity, [
            'headers' => [
              'accept' => 'application/json',
            ],
        ]);

        return $response->getBody();
    }

    /**
     * @OA\Post(
     *     path="auth/crawler-person",
     *     summary="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     description="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     tags={"that's illegal sir"},
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
    public function crawlerPerson()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("crawlerPerson");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "crawlerPerson");

        $client = new \GuzzleHttp\Client();

        $name = request()->name;

        $response = $client->request('GET', 'https://serpapi.com/search.json?api_key=' . CommonUtilitary::SERPAPI_KEY . '&engine=google&q=' . $name, [
            'headers' => [
              'accept' => 'application/json',
            ],
        ]);

        return $response->getBody();
    }

    /**
     * @OA\Post(
     *     path="auth/random-image-generator",
     *     summary="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     description="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     tags={"generator"},
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
    public function randomImageGenerator()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("randomImageGenerator");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "randomImageGenerator");

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://random.imagecdn.app/500/300', [
            'headers' => [
              'accept' => 'application/json',
            ],
        ]);

        $imageContent = $response->getBody()->getContents();
        $contentType = $response->getHeaderLine('Content-Type');
    
        return response($imageContent, 200)->header('Content-Type', $contentType);
    }

    /**
     * @OA\Post(
     *     path="auth/fake-identity-generator",
     *     summary="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     description="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     tags={"generator"},
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
    public function fakeIdentityGenerator()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("fakeIdentityGenerator");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "fakeIdentityGenerator");

        $quantity = request()->quantity;
        $faker = Factory::create();

        $listFaker = [];
        
        for ($i = 0; $i < $quantity; $i++) {
            $name = $faker->name();
            $email = $faker->email();

            $listFaker[] = [
                'name' => $name,
                'email' => $email,
            ];
        }

        return response()->json($listFaker);
    }

    /**
     * @OA\Post(
     *     path="auth/mail-spammer",
     *     summary="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     description="Vérifie si le mot de passe fait partie des mots de passe les plus fréquents",
     *     tags={"generator"},
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
    public function mailSpammer()
    {
        if (!Auth::id())
        {
            CommonUtilitary::UserNotConnected("mailSpammer");

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        CommonUtilitary::LogUsedFunction(Auth::id(), "mailSpammer");

        for ($i = 0; $i < request()->quantity; $i++)
        {
            Mail::send([], [], function (Message $message) {
                $message->to(request()->to)
                        ->from('epalestrasports@gmail.com', 'Envoi Spam')
                        ->subject(request()->subject)
                        ->html(request()->content);
            });
        }

        return 'Email envoyé avec succès!';
    }
}
