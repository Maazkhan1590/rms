<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';
    protected LoggingService $loggingService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LoggingService $loggingService)
    {
        $this->middleware('guest')->except('logout');
        $this->loggingService = $loggingService;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        // Check if user exists and status is not active
        if ($user && $user->status !== 'active') {
            // Log blocked login attempt due to pending approval
            $this->loggingService->logActivity(
                'login_blocked',
                "Login blocked - Account pending approval: {$user->name} ({$user->email})",
                'App\Models\User',
                $user->id,
                $user->id
            );
            
            // Store custom error message
            session()->flash('login_error', 'Your account is pending approval. Please wait for an administrator to approve your account.');
            session()->flash('login_email', $credentials['email']); // Store email for logging
            return false;
        }

        $attempt = $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );

        // If login attempt failed and user exists, log it
        if (!$attempt && $user) {
            $this->loggingService->logActivity(
                'login_failed',
                "Failed login attempt - Invalid password: {$user->name} ({$user->email})",
                'App\Models\User',
                $user->id,
                $user->id
            );
        } elseif (!$attempt) {
            // Log failed attempt with non-existent email
            $this->loggingService->logActivity(
                'login_failed',
                "Failed login attempt - Invalid email: {$credentials['email']}",
                null,
                null,
                null
            );
        }

        return $attempt;
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Check if there's a custom login error message
        if (session()->has('login_error')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => [session()->pull('login_error')],
            ]);
        }

        // Default failed login response
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * The user has been authenticated.
     * This method is called by Laravel's AuthenticatesUsers trait after successful login.
     * We clear url.intended here to prevent redirect to public pages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return void
     */
    protected function authenticated(Request $request, $user)
    {
        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        // Log successful login activity
        $this->loggingService->logActivity(
            'login_success',
            "Successful login: {$user->name} ({$user->email})",
            'App\Models\User',
            $user->id,
            $user->id
        );

        // CRITICAL: Clear url.intended BEFORE sendLoginResponse is called
        // This prevents Laravel from redirecting to public pages
        $request->session()->forget('url.intended');
        
        // Don't return redirect here - let sendLoginResponse handle it
        // This ensures sendLoginResponse is always called and handles the redirect
    }

    /**
     * Get the post-login redirect path.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectPath()
    {
        $user = auth()->user();
        
        // Faculty members go to public homepage
        if ($user && $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean()) {
            return '/';
        }
        
        // Admin, Coordinator, and Dean go to admin dashboard
        return '/admin';
    }

    /**
     * Send the response after the user was authenticated.
     * This overrides the trait method to redirect based on user role.
     * This is the FINAL method called by Laravel's AuthenticatesUsers trait.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        // CRITICAL: Clear url.intended BEFORE session regeneration
        // Session regeneration might copy old session data
        $request->session()->forget('url.intended');
        
        // Regenerate session for security
        $request->session()->regenerate();
        
        // Clear again AFTER regeneration to be absolutely sure
        $request->session()->forget('url.intended');
        
        $user = auth()->user();
        
        // Determine redirect based on user role
        if ($user && $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean()) {
            // Faculty members go to public homepage
            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('welcome')]);
            }
            return redirect()->route('welcome');
        }
        
        // Admin, Coordinator, and Dean go to admin dashboard
        if ($request->wantsJson()) {
            return response()->json(['redirect' => route('admin.home')]);
        }
        return redirect()->route('admin.home');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        
        // Log logout activity before logging out
        if ($user) {
            $this->loggingService->logActivity(
                'logout',
                "User logged out: {$user->name} ({$user->email})",
                'App\Models\User',
                $user->id,
                $user->id
            );
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * The user has been logged out of the application.
     */
    protected function loggedOut(Request $request)
    {
        // Logout activity already logged in logout() method
        return null;
    }
}
