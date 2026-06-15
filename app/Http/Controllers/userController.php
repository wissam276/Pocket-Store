<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::where('role', 'customer')->paginate(10);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'first_name' => 'required',
            'second_name' => 'required',
            'phone_number' => 'required|unique:users|min:10|max:10',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $validated['password'] = Hash::make($validated['password']);


        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
        ],201);


    }
    public function show(User $user)
    {
        return response()->json([
            'status' => true,
            'data'=>[
                'first_name' => $user->first_name,
                'second_name' => $user->second_name,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'password' => $user->password,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                 'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'is_active' => $user->is_active,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|required',
            'second_name' => 'sometimes',
            'phone_number' => 'sometimes|min:10|max:10|unique:users,phone_number,' . $user->id,
            'email'        => 'sometimes|email|unique:users,email,' . $user->id,
            'password'     => 'sometimes|min:8',
            'avatar' => 'sometimes|image',
        ]);

        // معالجة كلمة السر إذا تم إرسالها
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // معالجة الصورة
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // هنا الخطوة التي كانت ناقصة:
        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'updated successfully',
            'data' => $user
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(request $request, User $user)
    {
        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User has been deleted'
        ]);
    }
}
