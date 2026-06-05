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
        ]);
        $validated['password'] = Hash::make($validated['password']);
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
            'second_name' => 'required',
            'phone_number' => 'required|unique:users|min:10|max:10',
            'email' => 'required|email',
            'password' => 'required|min:8',
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
