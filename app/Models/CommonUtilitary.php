<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonUtilitary extends Model
{
    use HasFactory;

    const USER_NOT_CONNECTED = "Utilisateur non identifié";
    const API_HUNTER_IO = "https://api.hunter.io/v2/";
    const API_KEY_HUNTER_IO = "7033cdb5d241978193fd18d5537ca10ec4e1fa47";

    public static function UserNotConnected(string $function_name)
    {
        Logs::create([
            'usedFonction' => $function_name,
            'content' => CommonUtilitary::USER_NOT_CONNECTED
        ]);
    }

    public static function LogUsedFunction(int $user_id, string $function_name)
    {
        $username = User::find($user_id)['name'];

        Logs::create([
            'usedFonction' => $function_name,
            'content' => 'L\'utilisateur ' . $username . ' a utilisé la fonction "' . $function_name . '"',
            'user_id' => $user_id
        ]);
    }
}