<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListMessagesError extends Model
{
    use HasFactory;

    const USER_NOT_CONNECTED = "Utilisateur non identifié";
}
