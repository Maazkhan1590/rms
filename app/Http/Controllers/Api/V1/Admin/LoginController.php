<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\ApiResponser;

class LoginController extends Controller
{
    use ApiResponser;

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        // dd(Auth::attempt($request->all()));

        if (!Auth::attempt($request->all())) {
            return $this->error('Credentials not matched', 401);
        }

        return $this->success(
            [
                'token' => Auth::User()
                    ->createToken('user-token')
                    ->plainTextToken,
                'user' => Auth::user(),
            ],
            'Login successful'
        );

    }

    public function logout()
    {

        Auth::user()
            ->tokens()
            ->delete();

        return [
            'message' => 'Logged out successfully',
        ];
    }
}
