<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $response = Http::api()->post('/users/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $responseBody = json_decode($response->body());
            if (empty($responseBody->data)) {
                return back()->withErrors([
                    'message' => $responseBody->message,
                ]);
            }
            session([
                'api_token' => $responseBody->data->token,
                'user_name' => $responseBody->data->name,
                'user_email' => $responseBody->data->email,
            ]);

            $user = User::updateOrCreate(
                ['email' => $responseBody->data->email],
                [
                    'name' => $responseBody->data->name,
                    'password' => bcrypt(Str::random(32)),
                ]
            );

            Auth::login($user);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Hibás bejelentkezési adatok.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('api_token');

        return redirect('/');
    }
}
