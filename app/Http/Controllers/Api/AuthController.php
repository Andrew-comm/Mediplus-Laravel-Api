<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    /**
     * Register new user
     */
    public function register(Request $request)
    {


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],


            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],


            'password' => [
                'required',
                'string',
                'min:8'
            ]

        ]);



        $user = User::create([

            'name' => $validated['name'],


            'email' => $validated['email'],


            'password' => Hash::make(
                $validated['password']
            )

        ]);



        return response()->json([

            'message' => 'User registered successfully',

            'user' => $user

        ],201);


    }





    /**
     * Login user
     */
   public function login(Request $request)
{
    $credentials = $request->validate([

        'email' => ['required', 'email'],

        'password' => ['required']

    ]);


    if (!Auth::attempt($credentials)) {

        return response()->json([

            'message' => 'Invalid email or password'

        ], 401);

    }


    /** @var \App\Models\User $user */
    $user = Auth::user();


    $token = $user->createToken(
        'mediplus-token'
    )->plainTextToken;


    return response()->json([

        'message' => 'Login successful',

        'token' => $token,

        'user' => $user

    ]);
}









    /**
     * Get currently logged in user
     */
    public function user(Request $request)
    {


        return response()->json([

            'user'=>$request->user()

        ]);


    }





    /**
     * Logout user
     */
    public function logout(Request $request)
    {


        // Delete current token

        $request
            ->user()
            ->currentAccessToken()
            ->delete();



        return response()->json([

            'message'=>'Logged out successfully'

        ]);


    }



}
