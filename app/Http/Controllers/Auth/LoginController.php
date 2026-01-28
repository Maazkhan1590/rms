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
        if ($user->status !== 'active') {
            auth()->logout();

            // Return early with error - this will prevent sendLoginResponse from being called
            redirect()->route('login')->withErrors([
                'email' => 'Your account is currently ' . $user->status . '. Please contact an administrator.',
            ])->send();
            exit;
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        // Log activity
        $this->loggingService->logActivity(
            'login',
            "User logged in: {$user->name} ({$user->email})",
            null,
            null,
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
     * The user has been logged out of the application.
     */
    protected function loggedOut(Request $request)
    {
        if ($request->user()) {
            // Log activity
            $this->loggingService->logActivity(
                'logout',
                "User logged out: {$request->user()->name} ({$request->user()->email})",
                null,
                null,
                $request->user()->id
            );
        }
    }
}
