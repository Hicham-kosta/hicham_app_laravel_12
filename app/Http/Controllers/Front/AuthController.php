<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Front\ForgotPasswordRequest;
use App\Http\Requests\Front\ResetPasswordRequest;
use App\Http\Requests\Front\RegisterRequest;
use App\Http\Requests\Front\LoginRequest;
use App\Services\Front\AuthService;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\CustomResetPassword;

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

    /**
     * Show forgot password form
     */
    public function showForgotForm(){
        return view('front.auth.forgot_password');
    }

    /**
     * Handle forgot password dend reset link
     */
    public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email'),
        function ($user, $token) {
            // Use your custom notification
            $user->notify(new CustomResetPassword($token));
        }
    );

    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => __($status)])
        : response()->json(['errors' => ['email' => [__($status)]]], 422);
}

    /**
     * Show reset password form (link from email)
     */

    public function showResetForm(Request $request, $token)
{
    return view('front.auth.reset_password', [
        'token' => $token,
        'email' => $request->email
    ]);
}


    /**
     * Handle reset password POST
     */

    public function resetPassword(ResetPasswordRequest $request)
     {
    try {
        Log::info('Password reset attempt for email: ' . $request->email);
        Log::info('Token received: ' . $request->token);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
                Auth::login($user);
            }
        );

        Log::info('Password reset status: ' . $status);

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => __($status),
                'redirect' => url('/'),
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => ['email' => [__($status)]],
        ], 422);

    } catch (\Exception $e) {
        Log::error('Password reset error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred during password reset.',
        ], 500);
      }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'You are logged out successfully.');
    }
}
