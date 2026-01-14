<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RegistrationEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'employee_id'   => ['nullable', 'string', 'max:255', 'unique:users'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'college_id'    => ['nullable', 'exists:colleges,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'designation'   => ['nullable', 'string', 'max:255'],
            'orcid'         => ['nullable', 'string', 'max:19'],
            'google_scholar'=> ['nullable', 'string', 'max:500'],
            'research_gate' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'employee_id'   => $data['employee_id'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'college_id'    => $data['college_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'designation'   => $data['designation'] ?? null,
            'orcid'         => $data['orcid'] ?? null,
            'google_scholar'=> $data['google_scholar'] ?? null,
            'research_gate' => $data['research_gate'] ?? null,
            'status'        => 'pending', // Requires admin approval
        ]);

        // Assign Student role if exists
        $studentRole = \App\Models\Role::where('title', 'Student')->orWhere('title', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        } else {
            // Fallback: try to find any role or create Student role
            $studentRole = \App\Models\Role::firstOrCreate(
                ['title' => 'Student'],
                ['title' => 'Student']
            );
            $user->roles()->attach($studentRole->id);
        }

        return $user;
    }

    /**
     * Handle a registration request for the application.
     * Override to prevent auto-login and send email notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            // If AJAX request, return JSON with validation errors
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // For regular requests, redirect back with errors
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store password before hashing for email
        $plainPassword = $request->password;

        event(new Registered($user = $this->create($request->all())));

        // Send registration email with account details
        $user->notify(new RegistrationEmail($plainPassword));

        // Don't auto-login - user account is pending approval
        // $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // Redirect to home page with success message
        return $request->wantsJson() || $request->ajax()
                    ? new JsonResponse(['message' => 'Registration successful. Please check your email for account details.'], 201)
                    : redirect()->route('welcome')->with('success', 'Registration successful! Please check your email for account details. Your account is pending approval.');
    }
}
