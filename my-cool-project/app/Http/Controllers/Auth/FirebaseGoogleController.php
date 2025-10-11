<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;

class FirebaseGoogleController extends Controller
{
    public function login(Request $request)
    {
        $idToken = $request->input('token');
        $firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $auth = $firebase->createAuth();

        try {
            $verifiedIdToken = $auth->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Не удалось проверить токен Google.');
        }

        $uid = $verifiedIdToken->claims()->get('sub');
        $email = $verifiedIdToken->claims()->get('email');
        $name = $verifiedIdToken->claims()->get('name');
        
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'google_id' => $uid,
                'password' => Hash::make(str()->random(24))
            ]
        );

        Auth::login($user);
        
        return redirect()->route('home');
    }
}
