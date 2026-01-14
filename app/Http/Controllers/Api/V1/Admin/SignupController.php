<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\ApiResponser;

class SignupController extends Controller
{
    use ApiResponser;

    public function signup(Request $request){


        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ],
        [
            'password.required' => 'Password field is required',
            'password.min' => 'Minimum 8 characters should be in the password',
            // 'password.regex' => 'Password should have 1 Capital letter, 1 number, 1 lower and 1 special character'
        ]
        );

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        try {


            $user = User::create(['name'=>'User','email' => $request->email, 'password' => $request->password]);


            return $this->success(
                ['user' => $user], 'Signed up successfully. You can visit the website and login.'
            );


        }catch(\Exception $e){
            //Do something when query fails.
            if($e){
                return $this->error($e->getMessage(), 404, $request->all());
            }
        }




    }
}
