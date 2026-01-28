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
    protected $redirectTo = RouteServiceProvider::HOME;
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

    protected function authenticated(Request $request, $user)
    {
        if ($user->status !== 'active') {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is currently ' . $user->status . '. Please contact an administrator.',
            ]);
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

        // Student role is treated as Faculty role - redirect to admin dashboard
        // No special redirect needed, default behavior applies
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
