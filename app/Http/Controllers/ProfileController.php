<?php

namespace App\Http\Controllers;

use App\Models\ListMessagesError;
use App\Models\Logs;
use App\Models\Profiles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/changeprofile",
     *     summary="Change current user profile",
     *     tags={"profiles"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function changeprofile()
    {
        if (!Auth::id())
        {
            Logs::create([
                'usedFonction' => 'changeprofile',
                'content' => ListMessagesError::USER_NOT_CONNECTED
            ]);

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }

        $profileName = request(['profile_name']);
        
        if (empty($profileName))
        {
            Logs::create([
                'usedFonction' => 'changeprofile',
                'content' => 'Aucun nom de profil renseigné',
                'user_id' => Auth::id()
            ]);

            return response()->json(['erreur' => 'profile_name vide']);
        }

        $profile = Profiles::where('name', $profileName['profile_name'])->first();
        
        if ($profile) {
            $user = User::find(Auth::id());

            if ($user) {
                $user->profiles_id = $profile->id;
                $user->save();

                Logs::create([
                    'usedFonction' => 'changeprofile',
                    'content' => 'Changement du profil en ' . $profile['name'],
                    'user_id' => Auth::id()
                ]);

                return response()->json(['message' => 'Utilisateur mis à jour avec succès.']);
            } else {
                Logs::create([
                    'usedFonction' => 'changeprofile',
                    'content' => ListMessagesError::USER_NOT_CONNECTED
                ]);

                return response()->json(['erreur' => 'Utilisateur introuvable.'], 404);
            }
        } else {
            Logs::create([
                'usedFonction' => 'changeprofile',
                'content' => 'Nom de profil introuvable : ' . $profileName['profile_name'],
                'user_id' => Auth::id()
            ]);

            return response()->json(['erreur' => 'Profil introuvable.'], 404);
        }

        return response()->json(Auth::user());
    }

    /**
     * @OA\Get(
     *     path="/changerights",
     *     summary="Change profile's rights",
     *     tags={"profiles"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function changerights()
    {
        if (!Auth::id())
        {
            Logs::create([
                'usedFonction' => 'changerights',
                'content' => ListMessagesError::USER_NOT_CONNECTED
            ]);

            return response()->json(['erreur' => 'Utilisateur non authentifié'], 401);
        }


    }
}
