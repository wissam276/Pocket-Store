<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;




class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'second_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:20|confirmed',
            'phone_number' => 'required|unique:users',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "please check all fields",
                'errors' => $validator->errors()
            ], 422);
        }


        $data = $request->except('avatar', 'password', 'password_confirmation');
        $data['password'] = Hash::make($request->password);


        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }


        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => "Account has been created successfully",
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
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


    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // إرسال رابط إعادة التعيين (يستخدم الإعدادات في config/auth.php)
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'تم إرسال رابط إعادة التعيين إلى بريدك'])
            : response()->json(['message' => 'حدث خطأ أثناء الإرسال'], 400);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'password reset succesfuly'])
            : response()->json(['message' => 'Token is invalid or expired'], 400);
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'first_name'  => 'sometimes|string|max:255',
            'second_name'  => 'sometimes|string|max:255',
            'phone_number'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        if ($request->has('first_name')) $user->first_name  = $request->first_name;
        if ($request->has('second_name')) $user->second_name = $request->second_name;
        if ($request->has('phone_number')) $user->phone_number = $request->phone_number;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = bcrypt($request->password);

        $user->save();

        return response()->json([
            'message' => 'your data has been updated successfully',
            'user'    => $user
        ]);
    }
}
