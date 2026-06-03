<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;




class AuthController extends Controller
{
  public function register(Request $request){
      $validator = Validator::make($request->all(),[
          'first_name' => 'required',
          'second_name' => 'required',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:6|max:20|confirmed',
          'password_confirmation' => 'required|min:6|max:20',
          'phone_number' => 'required|unique:users'
      ]);

      if($validator->fails()){
          return response()->json([
              'status'=>false,
              'message'=>"check of the data ..",
              'errors'=>$validator->errors()
              ],422);
      }


      $user = User::create([
          'first_name'   => $request->first_name,
          'second_name'  => $request->second_name,
          'email'        => $request->email,
          'phone_number' => $request->phone_number, // هكذا نأخذ القيمة الحقيقية
          'password'     => Hash::make($request->password),
          'role'         => $request->role,         // هكذا نأخذ القيمة الحقيقية
      ]);


    $token=$user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status'=>true,
        'message'=>"account created succesfuly",
        'user'=>$user,
        'access_token'=>$token,
        'token_type'=>'Bearer',
    ],201);

  }

  public function login(Request $request)
  {
      $fields = $request->validate([
          'email' => 'required|string|email',
          'password' => 'required|string',
      ]);

      $user = User::where('email', $fields['email'])->first();
      if (!$user || !Hash::check($fields['password'], $user->password)) {
          return response()->json([
              'status' => false,
              'message' => "wrong password, try again"
          ], 401);
      }
      $token = $user->createToken('auth_token')->plainTextToken;

      return response([
          'status' => true,
          'message' => "login succesfuly",
          'user' => $user,
          'access_token' => $token,
      ], 200);

  }

  public function logout(Request $request){
      $request->user()->currentAccessToken()->delete();
      return response()->json([
          'status'=>true,
          'message'=>"logout succesfuly",
      ],200);
  }
}
