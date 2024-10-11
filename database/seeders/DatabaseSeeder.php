<?php

namespace Database\Seeders;

use App\Models\Profilerights;
use App\Models\Profiles;
use App\Models\Rights;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Profiles::create(['name' => 'Super Admin']);
        Profiles::create(['name' => 'Admin']); 
        Profiles::create(['name' => 'Utilisateur premium']); 
        Profiles::create(['name' => 'Utilisateur']);

        User::factory()->create([
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'profiles_id' => 1
        ]);

        Rights::create(['name' => 'canChangeRights']);
        Rights::create(['name' => 'canUseIsMailExist']); 
        Rights::create(['name' => 'canUseSpamMail']);
        Rights::create(['name' => 'canUsePhising']);
        Rights::create(['name' => 'canUseIsPasswordMostUsed']);
        Rights::create(['name' => 'canUseGetAllDomains']);
        Rights::create(['name' => 'canUseDDOS']);
        Rights::create(['name' => 'canUseChangeRandomPicture']);
        Rights::create(['name' => 'canUseGetRandomIdentity']);
        Rights::create(['name' => 'canUseCrawler']);
        Rights::create(['name' => 'canUseGeneratePassword']);

        // Super Admin
        for ($i=1; $i <= 11 ; $i++) { 
            Profilerights::create([
                'profiles_id' => 1,
                'rights_id' => $i,
                'isRightActive' => true
            ]);
        }

        // Admin
        for ($i=1; $i <= 11 ; $i++) { 
            Profilerights::create([
                'profiles_id' => 2,
                'rights_id' => $i
            ]);
        }

        // Utilisateur Premium
        for ($i=1; $i <= 11 ; $i++) { 
            Profilerights::create([
                'profiles_id' => 3,
                'rights_id' => $i
            ]);
        }

        // Utilisateur
        for ($i=1; $i <= 11 ; $i++) { 
            Profilerights::create([
                'profiles_id' => 4,
                'rights_id' => $i
            ]);
        }

        DB::table('profilerights')
            ->where('profiles_id', 2)
            ->where('rights_id', 1)
            ->update(['isRightActive' => true]);
    }
}
