<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = Validator::make($request->all(),
        [
            'name' =>'required|max:255',
            'email' => 'email|required|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        if($validateData->fails())
        {
            return response(['message' => 'Validation error']);
        }

        $user = User::create([
            'name' =>$request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('auth_token')->accessToken;
        dd($token);
        return response(['user'=> $request->name,'token' => $token->token, 'message' => 'User registered successfully']);
    }

    public function login(Request $request)
    {
        $data = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);


        if($data->fails())
        {
            return response(['message' => 'Validation error']);
        }

        $user = User::where(['email' => $request->email])->first();

        if(!Auth::attempt(['email' => $request->email,'password' => $request->password]))
        {
            return response(['message' => 'User does not exist']);
        }

        $token = auth()->user()->createToken('auth_token')->accessToken;

        return response(['message' => 'User authenticated', 'token' => $token->token]);
    }
}
