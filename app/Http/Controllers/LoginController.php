<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $req)
    {
        // Validate all input including Turnstile
        $req->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        // Verify Cloudflare Turnstile token
        $turnstileResponse = $req->input('cf-turnstile-response');
        $secretKey = config('services.turnstile_secret_key');

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secretKey,
            'response' => $turnstileResponse,
            'remoteip' => $req->ip(),
        ]);

        $turnstileResult = $response->json();

        if (!$turnstileResult['success']) {
            $errorMessage = 'Security verification failed. Please try again.';

            // Provide more specific error message for debugging
            if (isset($turnstileResult['error-codes']) && !empty($turnstileResult['error-codes'])) {
                $errorMessage .= ' Error codes: ' . implode(', ', $turnstileResult['error-codes']);
            }

            return back()->withErrors([
                'cf-turnstile-response' => $errorMessage,
            ])->onlyInput('username');
        }

        // Prepare authentication credentials (only username and password)
        $credentials = [
            'username' => $req->input('username'),
            'password' => $req->input('password'),
        ];

        // Proceed with normal authentication
        if (Auth::attempt($credentials)) {
            $req->session()->regenerate();

            return redirect()->intended('admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
}
