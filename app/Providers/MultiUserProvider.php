<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\GuestUser;
use Illuminate\Validation\ValidationException;

class MultiUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        if ((int)$identifier >= 1000000000) {
            return GuestUser::find($identifier);
        }
        return User::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        if ((int)$identifier >= 1000000000) {
            $user = GuestUser::find($identifier);
        } else {
            $user = User::find($identifier);
        }

        if (!$user) return null;

        $rememberToken = $user->getRememberToken();
        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        // Search for user without considering password initially
        $queryCredentials = array_filter($credentials, function ($key) {
            return !str_contains($key, 'password');
        }, ARRAY_FILTER_USE_KEY);

        $legacyUser = User::where($queryCredentials)->first();
        if ($legacyUser) return $legacyUser;

        //Doesnt consider inactive users, as they will be handled in validateCredentials method
        //return GuestUser::where($queryCredentials)->where('is_active', true)->first();

        // Consider all guest users, active or not, and handle the active check in validateCredentials
        return GuestUser::where($queryCredentials)->first();
    }

    // public function validateCredentials(Authenticatable $user, array $credentials)
    // {
    //     $plain = $credentials['password'];
        
    //     if ($user instanceof GuestUser) {
    //         return Hash::check($plain, $user->getAuthPassword());
    //     }

    //     // For Legacy User
    //     return md5($plain) === $user->getAuthPassword();
    // }

public function validateCredentials(Authenticatable $user, array $credentials)
{
    $plain = $credentials['password'];

    if ($user instanceof GuestUser) {

        // If already inactive
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => 'Your guest account is inactive. Please contact Computer Section to reactivate it.',
            ]);
        }

        // Check expiration (90 days)
        if ($user->created_at->addDays(90)->isPast()) {
            $user->update(['is_active' => false]);

            throw ValidationException::withMessages([
                'username' => 'Your guest account has expired after 90 days. Please contact Computer Section to reactivate it.',
            ]);
            
        }
        
        //dd($user);

        return Hash::check($plain, $user->getAuthPassword());
    }

    // Legacy User
    return md5($plain) === $user->getAuthPassword();
}

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        return false;
    }
}
