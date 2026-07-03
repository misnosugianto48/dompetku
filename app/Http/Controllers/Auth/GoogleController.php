<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception) {
            return redirect()->route('login')->withErrors([
                'email' => 'Failed to log in with Google. Please try again.',
            ]);
        }

        if (empty($googleUser->getEmail())) {
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to retrieve email address from Google.',
            ]);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            } elseif ($user->google_id !== $googleUser->getId()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'This Google account does not match the linked account for this email.',
                ]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => null,
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
