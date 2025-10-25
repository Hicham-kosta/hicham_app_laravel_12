<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\RegisterRequest;
use App\Http\Requests\Front\LoginRequest;
use App\Services\Front\AuthService;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        return view('front.auth.login');
    }

    public function showRegister()
    {
        return view('front.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->registerUser($data);

        // tell static analyzer this is the App\Models\User instance
        /** @var \App\Models\User $user */
        // send email (Mailtrap)    
        Mail::to($user->email)->send(new UserRegistered($user));

        //Auto login
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'registered successfully.',
            'redirect' => url('/'),
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $credentials = ['email' => $data['email'], 'password' => $data['password']];
        // Pre check if inactive return frendly error
        $user = \App\Models\User::where('email', $data['email'])->first();
        if($user && $user->status === 0){
            return response()->json([
                'success' => false,
                'errors' => ['email' => ['Your account is inactive, please contact support.']],
         ], 422);
       }
       if ($this->authService->attemptLogin($credentials, $request->boolean('remember'))) {
           $request->session()->regenerate();
           return response()->json([
               'success' => true,
               'message' => 'logged in successfully.',
               'redirect' => url('/'),
           ]);
       }

       return response()->json([
           'success' => false,
           'errors' => ['email' => ['The provided credentials do not match our records.']],
       ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'You are logged out successfully.');
    }
}
