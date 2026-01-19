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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Notifications\CustomResetPassword;
use App\Services\Front\CartService;
use App\Models\Admin;
use App\Models\User;

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
        if ($request->user_type === 'Vendor') {
            return app(VendorController::class)->register($request);
        }
        $data = $request->validated();
        // Capture guest session id BEFORE registration
        $guestSessionId = Session::get('session_id') ?: $request->session()->getId();

        $user = $this->authService->registerUser($data);

        // tell static analyzer this is the App\Models\User instance
        /** @var \App\Models\User $user */
        // send email (Mailtrap)    
        Mail::to($user->email)->send(new UserRegistered($user));

        //Auto login
        Auth::login($user);

        // Merge guest cart to user cart
        app(CartService::class)->migrateGuestCartToUser($guestSessionId, Auth::id());
        
        return response()->json([
            'success' => true,
            'message' => 'registered successfully.',
            'redirect' => url('/'),
        ]);
    }

    /**
     * Handle user/vendor login request
     */

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        // Capture guest session id
        $guestSessionId = Session::get('session_id') ?: $request->session()->getId();

        /**
         * Vendor Login
         */
        if($data['user_type'] === 'Vendor'){
            $vendor = Admin::where('email', $data['email'])
            ->where('role', 'vendor')
            ->first();

            if(!$vendor){
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['Vendor not found.']]
                ], 422);
            }
            if(!$vendor->confirm){
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['Vendor not confirmed yet.']]
                ], 422);
            }
            if((int)$vendor->status === 0){
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['Vendor not active.']]
                ], 422);
            }
            if(!Hash::check($data['password'], $vendor->password)){
                return response()->json([
                    'success' => false,
                    'errors' => ['password' => ['Invalid password.']]
                ], 422);
            }

            // Login vendor using admin guard
            auth('admin')->login($vendor);
            return response()->json([
                'success' => true,
                'message' => 'You are logged in successfully as Vendor.',
                'redirect' => url('/admin/dashboard'),
            ]);
        }

        /**
         * Customer Login
         */
        $credentials = ['email' => $data['email'], 'password' => $data['password']];
        $user = User::where('email', $data['email'])->first();
        if($user && (int)$user->status === 0){
            return response()->json([
                'success' => false,
                'errors' => ['email' => ['User not active, please contact support.']]
            ], 422);
        }
        if($this->authService->attemptLogin($credentials, $request->boolean('remember'))){
            app(CartService::class)->migrateGuestCartToUser($guestSessionId, auth()->id());
            return response()->json([
                'success' => true,
                'message' => 'User logged in successfully.',
                'redirect' => url('/'),
            ]);
        }
        return response()->json([
            'success' => false,
            'errors' => ['email' => ['Invalid credentials.']]
        ], 422);
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
